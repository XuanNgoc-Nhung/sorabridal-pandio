    <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\HasMany;

    class TrangPhuc extends Model
    {
        use HasFactory;

        protected $table = 'trang_phuc';

        /** Trạng thái: hiển thị */
        public const TRANG_THAI_ACTIVE = 1;

        /** Trạng thái: ẩn */
        public const TRANG_THAI_INACTIVE = 0;

        public const SAP_XEP_ID = 'id';

        public const SAP_XEP_TEN = 'ten_san_pham';

        public const SAP_XEP_MA = 'ma_san_pham';

        public const SAP_XEP_GIA_TRI = 'gia_tri';

        public const SAP_XEP_TRANG_THAI = 'trang_thai';

        public const SAP_XEP_CREATED_AT = 'created_at';

        public const SAP_XEP_MAC_DINH = self::SAP_XEP_ID;

        /** @var list<int> */
        public const PHAN_TRANG_OPTIONS = [24, 48, 96, 192];

        public const PHAN_TRANG_MAC_DINH = 48;

        public const LOC_HINH_ANH_CO = 'co';

        public const LOC_HINH_ANH_CHUA = 'chua';

        /** @var array<string, string> */
        public const LOC_HINH_ANH_OPTIONS = [
            '' => 'Tất cả',
            self::LOC_HINH_ANH_CO => 'Đã có ảnh',
            self::LOC_HINH_ANH_CHUA => 'Chưa có ảnh',
        ];

        /** @var array<string, string> */
        public const SAP_XEP_OPTIONS = [
            self::SAP_XEP_ID => 'Mới nhất',
            self::SAP_XEP_TEN => 'Tên sản phẩm',
            self::SAP_XEP_MA => 'Mã sản phẩm',
            self::SAP_XEP_GIA_TRI => 'Giá trị',
            self::SAP_XEP_TRANG_THAI => 'Trạng thái',
            self::SAP_XEP_CREATED_AT => 'Ngày tạo',
        ];

        /**
         * The attributes that are mass assignable.
         *
         * @var list<string>
         */
        protected $fillable = [
            'ten_san_pham',
            'ma_san_pham',
            'ngay_nhap',
            'hinh_anh',
            'ghi_chu',
            'trang_thai',
            'gia_tri',
        ];

        /**
         * Get the attributes that should be cast.
         *
         * @return array<string, string>
         */
        protected function casts(): array
        {
            return [
                'gia_tri' => 'decimal:2',
                'trang_thai' => 'integer',
            ];
        }

        /**
         * Sản phẩm được dùng trong nhiều dòng cho thuê.
         */
        public function sanPhamChoThue(): HasMany
        {
            return $this->hasMany(SanPhamChoThue::class, 'san_pham_id', 'id');
        }

        /**
         * Sản phẩm được gắn vào hợp đồng cưới.
         */
        public function hopDongCuoiTrangPhuc(): HasMany
        {
            return $this->hasMany(HopDongCuoiTrangPhuc::class, 'trang_phuc_id', 'id');
        }

        public static function perPageSanPham(?\Illuminate\Http\Request $request = null): int
        {
            $request ??= request();
            $perPage = (int) $request->query('per_page', self::PHAN_TRANG_MAC_DINH);

            return in_array($perPage, self::PHAN_TRANG_OPTIONS, true) ? $perPage : self::PHAN_TRANG_MAC_DINH;
        }
    }
