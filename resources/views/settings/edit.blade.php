@php
    $value = fn (string $key, string $default = '') => old($key, $settings[$key] ?? $default);
@endphp

<x-app-layout title="Pengaturan Sekolah">
    <div class="mx-auto max-w-5xl space-y-5">
        <p class="text-sm text-gray-500">Pengaturan ini dipakai di website publik, laporan PDF, dan validasi radius absensi guru.</p>

        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <x-ui.card title="Identitas Sekolah">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label for="school_name" value="Nama sekolah" />
                        <x-text-input id="school_name" name="school_name" :value="$value('school_name')" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('school_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="school_npsn" value="NPSN" />
                        <x-text-input id="school_npsn" name="school_npsn" :value="$value('school_npsn')" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('school_npsn')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="school_headmaster" value="Nama kepala sekolah" />
                        <x-text-input id="school_headmaster" name="school_headmaster" :value="$value('school_headmaster')" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('school_headmaster')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="school_address" value="Alamat" />
                        <x-text-input id="school_address" name="school_address" :value="$value('school_address')" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('school_address')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="school_phone" value="Telepon" />
                        <x-text-input id="school_phone" name="school_phone" :value="$value('school_phone')" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('school_phone')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="school_email" value="Email" />
                        <x-text-input id="school_email" name="school_email" type="email" :value="$value('school_email')" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('school_email')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="school_website" value="Website" />
                        <x-text-input id="school_website" name="school_website" :value="$value('school_website')" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('school_website')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="school_logo" value="Logo sekolah" />
                        <div class="mt-1 flex items-center gap-4">
                            @if (! empty($settings['school_logo']))
                                <img src="{{ Storage::disk('public')->url($settings['school_logo']) }}" alt="Logo sekolah" class="h-16 w-16 rounded-lg border border-gray-200 object-contain bg-white p-1">
                            @else
                                <span class="flex h-16 w-16 items-center justify-center rounded-lg border border-dashed border-gray-300 text-xs text-gray-400">Belum ada</span>
                            @endif
                            <input id="school_logo" type="file" name="school_logo" accept=".jpg,.jpeg,.png,.webp"
                                   class="block w-full field-file">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG, atau WEBP maksimal 2 MB.</p>
                        <x-input-error :messages="$errors->get('school_logo')" class="mt-2" />
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Profil Singkat" description="Ditampilkan pada halaman profil sekolah.">
                <div class="space-y-4">
                    <div>
                        <x-input-label for="school_description" value="Deskripsi" />
                        <textarea id="school_description" name="school_description" rows="3"
                                  class="field mt-1">{{ $value('school_description') }}</textarea>
                        <x-input-error :messages="$errors->get('school_description')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="school_vision" value="Visi" />
                        <textarea id="school_vision" name="school_vision" rows="2"
                                  class="field mt-1">{{ $value('school_vision') }}</textarea>
                        <x-input-error :messages="$errors->get('school_vision')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="school_mission" value="Misi (satu baris per misi)" />
                        <textarea id="school_mission" name="school_mission" rows="4"
                                  class="field mt-1">{{ $value('school_mission') }}</textarea>
                        <x-input-error :messages="$errors->get('school_mission')" class="mt-2" />
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Koordinat & Radius Absensi" description="Guru hanya dapat absen dalam radius ini dari titik sekolah.">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="school_latitude" value="Latitude" />
                        <x-text-input id="school_latitude" name="school_latitude" :value="$value('school_latitude')" class="mt-1 block w-full" placeholder="Contoh: 1.4748000" />
                        <x-input-error :messages="$errors->get('school_latitude')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="school_longitude" value="Longitude" />
                        <x-text-input id="school_longitude" name="school_longitude" :value="$value('school_longitude')" class="mt-1 block w-full" placeholder="Contoh: 124.8421000" />
                        <x-input-error :messages="$errors->get('school_longitude')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="geofence_radius" value="Radius (meter)" />
                        <x-text-input id="geofence_radius" name="geofence_radius" type="number" min="10" max="1000" :value="$value('geofence_radius', (string) $radius)" class="mt-1 block w-full" required />
                        <p class="mt-1 text-xs text-gray-500">Rekomendasi 50 meter. Saat ini: {{ $radius }} meter.</p>
                        <x-input-error :messages="$errors->get('geofence_radius')" class="mt-2" />
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 text-xs text-gray-600 ring-1 ring-gray-200">
                        <p class="font-semibold text-gray-800">Cara mengubah koordinat</p>
                        <ol class="mt-2 list-inside list-decimal space-y-1">
                            <li>Buka Google Maps, cari lokasi sekolah.</li>
                            <li>Klik kanan pada titik sekolah, salin angka latitude &amp; longitude.</li>
                            <li>Tempel pada kolom di samping, lalu simpan.</li>
                        </ol>
                        @if (! empty($settings['school_latitude']) && ! empty($settings['school_longitude']))
                            <a href="https://www.google.com/maps?q={{ $settings['school_latitude'] }},{{ $settings['school_longitude'] }}"
                               target="_blank" rel="noopener" class="mt-3 inline-block font-semibold text-brand-600 hover:text-brand-700">
                                Buka titik saat ini di Google Maps &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Jam Absensi">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <x-input-label for="check_in_time" value="Jam mulai absen" />
                        <x-text-input id="check_in_time" name="check_in_time" type="time" :value="$value('check_in_time', '06:30')" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('check_in_time')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="late_after" value="Batas terlambat" />
                        <x-text-input id="late_after" name="late_after" type="time" :value="$value('late_after', '07:15')" class="mt-1 block w-full" required />
                        <p class="mt-1 text-xs text-gray-500">Absen setelah jam ini dicatat terlambat.</p>
                        <x-input-error :messages="$errors->get('late_after')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="check_out_time" value="Jam pulang" />
                        <x-text-input id="check_out_time" name="check_out_time" type="time" :value="$value('check_out_time', '14:00')" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('check_out_time')" class="mt-2" />
                    </div>
                </div>
            </x-ui.card>

            <div class="flex items-center gap-3">
                <button class="btn btn-primary">Simpan Pengaturan</button>
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
