<option value=""></option>
@foreach(($danhSachCaLamViec ?? []) as $ca)
    <option value="{{ $ca->id }}" @selected((int) ($selected ?? null) === (int) $ca->id)>
        @if($chiTen ?? false)
            {{ $ca->ten_ca }}
        @else
            {{ $ca->ten_ca }} ({{ $ca->gioBatDauHienThi() }} – {{ $ca->gioKetThucHienThi() }})
        @endif
    </option>
@endforeach
