<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hop_dong_cuoi', function (Blueprint $table) {
            $table->id();
            $table->string('ma_hop_dong', 30)->unique();
            $table->string('loai_hop_dong', 100)->nullable();
            $table->string('ten_co_dau', 150);
            $table->string('ten_chu_re', 150);
            $table->text('email_sdt_co_dau')->nullable();
            $table->text('email_sdt_chu_re')->nullable();
            $table->date('ngay_chup_du_kien')->nullable();
            $table->date('ngay_chup_thuc_te')->nullable();
            $table->time('gio_chup')->nullable();
            $table->enum('buoi_chup', ['sang', 'chieu', 'ca_ngay'])->nullable();
            $table->date('ngay_cuoi_du_kien')->nullable();
            $table->date('ngay_cuoi_chinh_thuc')->nullable();
            $table->text('dia_diem_chup')->nullable();
            $table->foreignId('concept_id')->nullable()->constrained('concept')->nullOnDelete();
            $table->enum('loai_dich_vu', [
                'combo_tron_goi',
                'ghep_dich_vu_le',
                'combo_va_nang_cap',
            ])->nullable();
            $table->bigInteger('nhom_dich_vu_id')->default(-1);
            $table->string('kenh_tiep_can', 100)->nullable();
            $table->text('yeu_cau_dac_biet')->nullable();
            $table->decimal('tong_tien', 15, 2)->default(0);
            $table->decimal('chiet_khau', 15, 2)->default(0);
            $table->decimal('tien_coc', 15, 2)->default(0);
            $table->enum('trang_thai_hop_dong', [
                'nhap',
                'da_huy',
                'dang_thuc_hien',
                'tre_chup',
                'tre_edit',
            ])->default('nhap');
            $table->string('link_demo', 500)->nullable();
            $table->date('ngay_tra_link_demo_du_kien')->nullable();
            $table->date('ngay_tra_link_demo_chinh_thuc')->nullable();
            $table->dateTime('ngay_up_link_demo_gan_nhat')->nullable();
            $table->foreignId('nguoi_up_link_demo_id')->nullable()->constrained('nhan_vien')->nullOnDelete();
            $table->string('link_in', 500)->nullable();
            $table->date('ngay_tra_link_in_du_kien')->nullable();
            $table->date('ngay_tra_link_in_chinh_thuc')->nullable();
            $table->dateTime('ngay_up_link_in_gan_nhat')->nullable();
            $table->foreignId('nguoi_up_link_in_id')->nullable()->constrained('nhan_vien')->nullOnDelete();
            $table->text('ghi_chu_sale')->nullable();
            $table->foreignId('tho_chup_id')->nullable()->constrained('nhan_vien')->nullOnDelete();
            $table->foreignId('tho_make_id')->nullable()->constrained('nhan_vien')->nullOnDelete();
            $table->foreignId('tho_edit_id')->nullable()->constrained('nhan_vien')->nullOnDelete();
            $table->date('ngay_ky_hop_dong')->nullable();
            $table->date('han_thanh_toan_lan2')->nullable();
            $table->date('han_thanh_toan_lan3')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('nhan_vien')->nullOnDelete();
        });

        Schema::create('hop_dong_thanh_toan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hop_dong_id')->constrained('hop_dong_cuoi')->cascadeOnDelete();
            $table->unsignedTinyInteger('lan_thanh_toan');
            $table->decimal('so_tien', 15, 2);
            $table->date('ngay_thanh_toan');
            $table->string('hinh_thuc_thanh_toan', 50)->nullable();
            $table->json('proof_urls')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->foreignId('created_by')->nullable()->constrained('nhan_vien')->nullOnDelete();
        });

        Schema::create('hop_dong_cuoi_nhom_dich_vu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dich_vu_le_id')->constrained('dich_vu_le')->cascadeOnDelete();
            $table->foreignId('nhom_dich_vu_id')->constrained('nhom_dich_vu')->cascadeOnDelete();
            $table->foreignId('hop_dong_cuoi_id')->constrained('hop_dong_cuoi')->cascadeOnDelete();
            $table->unsignedTinyInteger('trang_thai_su_dung')->default(0);
            $table->timestamps();

            $table->unique(
                ['hop_dong_cuoi_id', 'nhom_dich_vu_id', 'dich_vu_le_id'],
                'uniq_ndvlc_hdc_ndv_dvl'
            );
        });

        Schema::create('hop_dong_cuoi_dich_vu_le', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dich_vu_le_id')->constrained('dich_vu_le')->cascadeOnDelete();
            $table->foreignId('hop_dong_cuoi_id')->constrained('hop_dong_cuoi')->cascadeOnDelete();
            $table->unsignedInteger('so_luong')->default(1);
            $table->timestamps();

            $table->unique(['hop_dong_cuoi_id', 'dich_vu_le_id'], 'uniq_hdc_dvlc_pair');
        });

        Schema::create('hop_dong_cuoi_trang_phuc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hop_dong_cuoi_id')->constrained('hop_dong_cuoi')->cascadeOnDelete();
            $table->foreignId('trang_phuc_id')->constrained('trang_phuc')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['hop_dong_cuoi_id', 'trang_phuc_id'], 'uniq_hdc_tp_pair');
        });

        Schema::create('hop_dong_cuoi_thanh_vien_sale', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hop_dong_id')->constrained('hop_dong_cuoi')->cascadeOnDelete();
            $table->foreignId('nhan_vien_id')->constrained('nhan_vien')->cascadeOnDelete();
            $table->enum('vai_tro', ['nguoi_tao', 'thanh_vien']);
            $table->timestamps();

            $table->unique(['hop_dong_id', 'nhan_vien_id', 'vai_tro'], 'uniq_hdc_tv_sale_hop_nv_role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hop_dong_cuoi_thanh_vien_sale');
        Schema::dropIfExists('hop_dong_cuoi_trang_phuc');
        Schema::dropIfExists('hop_dong_cuoi_dich_vu_le');
        Schema::dropIfExists('hop_dong_cuoi_nhom_dich_vu');
        Schema::dropIfExists('hop_dong_thanh_toan');
        Schema::dropIfExists('hop_dong_cuoi');
    }
};
