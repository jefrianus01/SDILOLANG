@php
    $user = auth()->user();
    $todayAttendance = $today;
@endphp

<x-app-layout title="Absensi Saya">
    <div class="space-y-5">
        <x-ui.page-header
            eyebrow="Absensi Guru"
            :title="$teacher->nama"
            :description="($teacher->teacherType?->name ?? 'Guru').' · NIP/NIK '.$teacher->nip_nik"
        >
            <x-slot name="actions">
                <a href="{{ route('profile.edit') }}" class="btn btn-secondary">Profil saya</a>
            </x-slot>
        </x-ui.page-header>

        <div class="flex flex-wrap items-center gap-2">
            <span class="chip">
                <x-ui.icon name="map-pin" class="h-3.5 w-3.5 text-gray-400" />
                Radius absensi <span class="font-semibold text-gray-800">{{ $radius }} m</span>
            </span>
            <span class="chip">
                <x-ui.icon name="clock" class="h-3.5 w-3.5 text-gray-400" />
                Batas terlambat <span class="font-semibold text-gray-800">{{ $lateAfter }}</span>
            </span>
            <span class="chip">
                <x-ui.icon name="calendar" class="h-3.5 w-3.5 text-gray-400" />
                {{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>

        @if (! $hasCoordinates)
            <x-ui.alert type="warning">
                Koordinat sekolah belum diatur. Hubungi Kepala Sekolah agar absensi berbasis lokasi dapat digunakan.
            </x-ui.alert>
        @endif

        <div class="grid gap-5 lg:grid-cols-3">
            <x-ui.card title="Absensi Hari Ini" class="lg:col-span-1">
                @if ($todayAttendance)
                    <dl class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Status</dt>
                            <dd><x-ui.badge :status="$todayAttendance->status->value" /></dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Persetujuan</dt>
                            <dd><x-ui.badge :status="$todayAttendance->approval_status->value" /></dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Masuk</dt>
                            <dd class="font-semibold text-gray-900">{{ $todayAttendance->check_in_at?->format('H:i') ?? '-' }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Pulang</dt>
                            <dd class="font-semibold text-gray-900">{{ $todayAttendance->check_out_at?->format('H:i') ?? '-' }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Jarak saat masuk</dt>
                            <dd class="font-semibold text-gray-900">{{ $todayAttendance->check_in_distance !== null ? $todayAttendance->check_in_distance.' m' : '-' }}</dd>
                        </div>
                        @if ($todayAttendance->reason)
                            <div class="rounded-lg bg-gray-50 p-3">
                                <dt class="text-gray-500">Keterangan</dt>
                                <dd class="mt-1 text-gray-700">{{ $todayAttendance->reason }}</dd>
                            </div>
                        @endif
                    </dl>
                @else
                    <p class="text-sm text-gray-500">Anda belum melakukan absensi hari ini.</p>
                @endif

                <div class="mt-5 space-y-3">
                    @if (! $todayAttendance || ! $todayAttendance->check_in_at)
                        <form method="POST" action="{{ route('teacher-attendance.check-in') }}"
                              x-data="{
                                  loading: false,
                                  error: '',
                                  locate(event) {
                                      event.preventDefault();
                                      if (!navigator.geolocation) { this.error = 'Browser tidak mendukung geolokasi.'; return; }
                                      this.loading = true;
                                      this.error = '';
                                      navigator.geolocation.getCurrentPosition(
                                          (position) => {
                                              this.loading = false;
                                              event.target.querySelector('[name=latitude]').value = position.coords.latitude;
                                              event.target.querySelector('[name=longitude]').value = position.coords.longitude;
                                              event.target.submit();
                                          },
                                          (failure) => {
                                              this.loading = false;
                                              this.error = 'Gagal mengambil lokasi: ' + failure.message;
                                          },
                                          { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                                      );
                                  }
                              }"
                              @submit="locate($event)">
                            @csrf
                            <input type="hidden" name="latitude" value="">
                            <input type="hidden" name="longitude" value="">
                            <button type="submit" :disabled="loading"
                                    class="btn btn-primary w-full py-2.5 disabled:cursor-not-allowed">
                                <span x-show="! loading">Absen Masuk</span>
                                <span x-show="loading" x-cloak>Mengambil lokasi...</span>
                            </button>
                            <p x-show="error" x-cloak x-text="error" class="mt-2 text-xs font-semibold text-rose-600"></p>
                            <p class="mt-2 text-xs text-gray-500">Izinkan akses lokasi agar sistem dapat memastikan Anda berada di area sekolah.</p>
                        </form>
                    @endif

                    @if ($todayAttendance && $todayAttendance->check_in_at && ! $todayAttendance->check_out_at)
                        <form method="POST" action="{{ route('teacher-attendance.check-out') }}"
                              x-data="{
                                  loading: false,
                                  error: '',
                                  locate(event) {
                                      event.preventDefault();
                                      if (!navigator.geolocation) { this.error = 'Browser tidak mendukung geolokasi.'; return; }
                                      this.loading = true;
                                      this.error = '';
                                      navigator.geolocation.getCurrentPosition(
                                          (position) => {
                                              this.loading = false;
                                              event.target.querySelector('[name=latitude]').value = position.coords.latitude;
                                              event.target.querySelector('[name=longitude]').value = position.coords.longitude;
                                              event.target.submit();
                                          },
                                          (failure) => {
                                              this.loading = false;
                                              this.error = 'Gagal mengambil lokasi: ' + failure.message;
                                          },
                                          { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                                      );
                                  }
                              }"
                              @submit="locate($event)">
                            @csrf
                            <input type="hidden" name="latitude" value="">
                            <input type="hidden" name="longitude" value="">
                            <button type="submit" :disabled="loading"
                                    class="btn btn-secondary w-full py-2.5 disabled:cursor-not-allowed">
                                <span x-show="! loading">Absen Pulang</span>
                                <span x-show="loading" x-cloak>Mengambil lokasi...</span>
                            </button>
                            <p x-show="error" x-cloak x-text="error" class="mt-2 text-xs font-semibold text-rose-600"></p>
                        </form>
                    @endif
                </div>
            </x-ui.card>

            <x-ui.card title="Ajukan Izin / Dinas Luar" description="Pengajuan akan ditinjau Kepala Sekolah." class="lg:col-span-2">
                <form method="POST" action="{{ route('teacher-attendance.permission') }}" enctype="multipart/form-data" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    <div>
                        <x-input-label for="date" value="Tanggal" />
                        <x-text-input id="date" name="date" type="date" :value="old('date', now()->toDateString())" max="{{ now()->toDateString() }}" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('date')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="type" value="Jenis pengajuan" />
                        <select id="type" name="type" required
                                class="field mt-1">
                            <option value="izin" @selected(old('type') === 'izin')>Izin</option>
                            <option value="dinas_luar" @selected(old('type') === 'dinas_luar')>Dinas Luar</option>
                            <option value="sakit" @selected(old('type') === 'sakit')>Sakit</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="reason" value="Alasan / keterangan" />
                        <textarea id="reason" name="reason" rows="3" required
                                  class="field mt-1">{{ old('reason') }}</textarea>
                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="attachment" value="Lampiran (opsional)" />
                        <input id="attachment" type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png"
                               class="mt-1 block w-full field-file">
                        <p class="mt-1 text-xs text-gray-500">PDF atau gambar, maksimal 4 MB.</p>
                        <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <button class="btn btn-primary">Kirim Pengajuan</button>
                    </div>
                </form>
            </x-ui.card>
        </div>

        <x-ui.card title="Riwayat Absensi" description="12 data terbaru" padding="p-0">
            <div class="border-b border-gray-100 px-4 py-3">
                <form method="GET" action="{{ route('teacher-attendance.index') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <x-input-label for="month" value="Bulan" />
                        <input id="month" type="month" name="month" value="{{ request('month') }}"
                               class="field mt-1">
                    </div>
                    <button class="btn btn-primary">Tampilkan</button>
                    @if (request('month'))
                        <a href="{{ route('teacher-attendance.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Reset</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Masuk</th>
                            <th class="px-4 py-3">Pulang</th>
                            <th class="px-4 py-3">Jarak</th>
                            <th class="px-4 py-3">Persetujuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($history as $row)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $row->date->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3"><x-ui.badge :status="$row->status->value" /></td>
                                <td class="px-4 py-3 text-gray-700">{{ $row->check_in_at?->format('H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $row->check_out_at?->format('H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $row->check_in_distance !== null ? $row->check_in_distance.' m' : '-' }}</td>
                                <td class="px-4 py-3"><x-ui.badge :status="$row->approval_status->value" /></td>
                            </tr>
                        @empty
                            <x-ui.empty-row :colspan="6" message="Belum ada riwayat absensi." />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($history->hasPages())
                <div class="border-t border-gray-100 px-4 py-3">{{ $history->links() }}</div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
