/**
 * Cổng kết nối REST API giữa FE admin và backend Laravel.
 * Phụ thuộc: axios (window.axios), showAdminToast (admin-toast.js) — nạp trước file này.
 *
 * Gọi:
 *   RestApi.get('/admin/foo', { q: 'abc' })
 *   RestApi.post('/admin/foo', { name: '...' })
 *   RestApi.patch(url, { trang_thai: 1 })
 *   RestApi.put(url, formData)
 *   RestApi.delete(url, { id: 1 })
 *   RestApi.request(path, { method, data, params, headers, signal })
 *
 * Toast (mặc định tự động cho POST/PUT/PATCH/DELETE):
 *   toast: false | true | 'auto'   — tắt / bật cả GET / auto (mặc định)
 *   silent: true                  — tắt mọi toast
 *   toastSuccess: false            — không toast thành công
 *   toastError: false             — không toast lỗi
 *   successMessage, errorMessage  — ghi đè nội dung toast
 *
 * Kết quả luôn resolve (không reject) dạng:
 *   { ok: boolean, status: number, data: any, message?: string, errors?: object }
 */
(function () {
    if (window.__restApiInit) {
        return;
    }
    window.__restApiInit = true;

    var client = null;
    var MUTATION_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? (meta.getAttribute('content') || '') : '';
    }

    function ensureAxios() {
        if (!window.axios) {
            throw new Error('axios chưa được nạp. Hãy thêm script axios trước rest_api.js.');
        }
        return window.axios;
    }

    function isFormData(value) {
        return typeof FormData !== 'undefined' && value instanceof FormData;
    }

    function pickMessage(data, fallback) {
        if (!data || typeof data !== 'object') {
            return fallback;
        }
        if (typeof data.message === 'string' && data.message.trim()) {
            return data.message;
        }
        if (typeof data.error === 'string' && data.error.trim()) {
            return data.error;
        }
        if (data.errors && typeof data.errors === 'object') {
            var keys = Object.keys(data.errors);
            if (keys.length && Array.isArray(data.errors[keys[0]]) && data.errors[keys[0]][0]) {
                return data.errors[keys[0]][0];
            }
        }
        return fallback;
    }

    function flattenErrors(errors) {
        if (!errors || typeof errors !== 'object') {
            return [];
        }
        var lines = [];
        Object.keys(errors).forEach(function (key) {
            (errors[key] || []).forEach(function (line) {
                if (line) {
                    lines.push(String(line));
                }
            });
        });
        return lines;
    }

    function buildResult(ok, status, data, message, errors) {
        var result = {
            ok: !!ok,
            status: status || 0,
            data: data == null ? null : data
        };
        if (message) {
            result.message = message;
        }
        if (errors) {
            result.errors = errors;
        }
        return result;
    }

    function showToast(message, type, options) {
        if (!message) {
            return;
        }
        if (typeof window.showAdminToast === 'function') {
            window.showAdminToast(message, type, options || {});
            return;
        }
        window.alert(message);
    }

    function isMutationMethod(method) {
        return MUTATION_METHODS.indexOf(String(method || 'GET').toUpperCase()) !== -1;
    }

    function resolveToastFlags(method, options, result) {
        options = options || {};
        if (options.silent === true || options.toast === false) {
            return { success: false, error: false };
        }

        var explicit = options.toast === true;
        var isMutation = isMutationMethod(method);
        var auto = options.toast !== false && !explicit;

        var success = options.toastSuccess === true
            || ((explicit || (auto && isMutation)) && options.toastSuccess !== false);
        var error = options.toastError === true
            || ((explicit || (auto && isMutation)) && options.toastError !== false);

        if (result && !result.ok) {
            if (result.message === 'Request cancelled.') {
                error = false;
            }
            if (result.status === 422 && result.errors && options.toastError !== true && !options.errorMessage) {
                error = false;
            }
        }

        return { success: success, error: error };
    }

    function pickSuccessMessage(result, options) {
        if (options && options.successMessage) {
            return options.successMessage;
        }
        return pickMessage(result.data, '');
    }

    function isSuccessfulResult(result) {
        if (!result || !result.ok) {
            return false;
        }
        if (result.data && typeof result.data === 'object' && result.data.success === false) {
            return false;
        }
        return true;
    }

    function pickErrorMessage(result, options) {
        if (options && options.errorMessage) {
            return options.errorMessage;
        }
        var lines = flattenErrors(result.errors);
        if (lines.length) {
            return lines.join(' ');
        }
        if (result.data && typeof result.data === 'object' && result.data.success === false) {
            return pickMessage(result.data, result.message || 'Có lỗi xảy ra.');
        }
        return result.message || 'Có lỗi xảy ra.';
    }

    function notifyResult(method, options, result) {
        options = options || {};
        var flags = resolveToastFlags(method, options, result);
        var success = isSuccessfulResult(result);

        if (success) {
            if (!flags.success) {
                return;
            }
            var successMsg = pickSuccessMessage(result, options);
            if (successMsg) {
                showToast(successMsg, 'success');
            }
            return;
        }

        if (!flags.error) {
            return;
        }
        showToast(pickErrorMessage(result, options), 'error');
    }

    function buildAxiosConfig(options) {
        var config = {};
        if (options.params) {
            config.params = options.params;
        }
        if (options.headers) {
            config.headers = options.headers;
        }
        if (options.signal) {
            config.signal = options.signal;
        }
        if (options.timeout != null) {
            config.timeout = options.timeout;
        }
        if (options.responseType) {
            config.responseType = options.responseType;
        }
        if (options.onUploadProgress) {
            config.onUploadProgress = options.onUploadProgress;
        }
        return config;
    }

    function getClient() {
        if (client) {
            return client;
        }

        var axios = ensureAxios();
        client = axios.create({
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        });

        client.interceptors.request.use(function (config) {
            var csrf = getCsrfToken();
            if (csrf) {
                config.headers['X-CSRF-TOKEN'] = csrf;
            }

            var data = config.data;
            if (data != null && !isFormData(data) && typeof data === 'object' && !(data instanceof Blob)) {
                config.headers['Content-Type'] = 'application/json';
            }

            console.log('[RestApi] Request', {
                method: (config.method || 'GET').toUpperCase(),
                url: config.url,
                params: config.params || null,
                data: isFormData(data) ? '[FormData]' : data,
                headers: config.headers
            });

            return config;
        });

        client.interceptors.response.use(
            function (response) {
                console.log('[RestApi] Response', {
                    method: (response.config.method || 'GET').toUpperCase(),
                    url: response.config.url,
                    status: response.status,
                    data: response.data
                });
                return response;
            },
            function (error) {
                var response = error && error.response ? error.response : null;
                console.log('[RestApi] Response Error', {
                    method: error.config ? (error.config.method || 'GET').toUpperCase() : null,
                    url: error.config ? error.config.url : null,
                    status: response ? response.status : 0,
                    data: response ? response.data : null,
                    message: error.message || null
                });
                return Promise.reject(error);
            }
        );

        return client;
    }

    function request(path, options) {
        options = options || {};
        var method = String(options.method || 'GET').toUpperCase();
        var config = buildAxiosConfig(options);
        var payload = options.data !== undefined ? options.data : options.body;
        var axiosClient = getClient();

        var promise;
        if (method === 'GET') {
            promise = axiosClient.get(path, config);
        } else if (method === 'DELETE') {
            promise = axiosClient.delete(path, config);
        } else {
            config.method = method;
            config.url = path;
            config.data = payload;
            promise = axiosClient.request(config);
        }

        return promise
            .then(function (response) {
                var result = buildResult(true, response.status, response.data);
                notifyResult(method, options, result);
                return result;
            })
            .catch(function (error) {
                if (error && error.code === 'ERR_CANCELED') {
                    var cancelled = buildResult(false, 0, null, 'Request cancelled.');
                    return cancelled;
                }

                var response = error && error.response ? error.response : null;
                var status = response ? response.status : 0;
                var data = response ? response.data : null;
                var message = pickMessage(data, (error && error.message) || 'Có lỗi xảy ra.');
                var errors = data && data.errors ? data.errors : null;
                var result = buildResult(false, status, data, message, errors);

                notifyResult(method, options, result);
                return result;
            });
    }

    function get(path, params, options) {
        options = options || {};
        if (params) {
            options.params = params;
        }
        return request(path, Object.assign({}, options, { method: 'GET' }));
    }

    function post(path, data, options) {
        options = options || {};
        options.data = data;
        return request(path, Object.assign({}, options, { method: 'POST' }));
    }

    function put(path, data, options) {
        options = options || {};
        options.data = data;
        return request(path, Object.assign({}, options, { method: 'PUT' }));
    }

    function patch(path, data, options) {
        options = options || {};
        options.data = data;
        return request(path, Object.assign({}, options, { method: 'PATCH' }));
    }

    function del(path, params, options) {
        options = options || {};
        if (params) {
            options.params = params;
        }
        return request(path, Object.assign({}, options, { method: 'DELETE' }));
    }

    window.RestApi = {
        request: request,
        get: get,
        post: post,
        put: put,
        patch: patch,
        delete: del,
        getClient: getClient,
        getCsrfToken: getCsrfToken
    };

    window.restApi = window.RestApi;
})();
