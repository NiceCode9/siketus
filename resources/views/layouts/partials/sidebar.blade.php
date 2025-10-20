<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-home"></i>
                <p>
                    Dashoard
                </p>
            </a>
        </li>
        @if (auth()->user()->hasRole('admin'))
            <li class="nav-header">DATAMASTER</li>

            <li class="nav-item">
                <a href="{{ route('admin.tahun-akademik.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-calendar-alt"></i>
                    <p>
                        Tahun Akademik
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.jurusan.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-graduation-cap"></i>
                    <p>
                        Jurusan
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.kegiatan-keagamaan.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-pray"></i>
                    <p>
                        Kegiatan Keagamaan
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.kedisiplinan.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-user-check"></i>
                    <p>
                        Kedisiplinan
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.guru.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-chalkboard-teacher"></i>
                    <p>
                        Guru
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.kelas.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-graduation-cap"></i>
                    <p>
                        Kelas
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.siswa.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-user-graduate"></i>
                    <p>
                        Siswa
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.mapel.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-book"></i>
                    <p>
                        Mapel
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.guru-mapel.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-chalkboard-teacher"></i>
                    <p>
                        Guru Mapel
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.guru-kelas.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-chalkboard"></i>
                    <p>
                        Guru Kelas
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.jenis-ujian.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-file-signature"></i>
                    <p>
                        Jenis Ujian
                    </p>
                </a>
            </li>


            <li class="nav-header">PENJADWALAN</li>

            <li class="nav-item">
                <a href="{{ route('admin.kalender.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-calendar"></i>
                    <p>
                        Kalender Akademik
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.jadwal.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-clock"></i>
                    <p>
                        Jadwal Pelajaran
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.pertemuan.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-handshake"></i>
                    <p>
                        Pertemuan
                    </p>
                </a>
            </li>
        @endif


        @if (auth()->user()->hasRole('guru'))
            <li class="nav-header">ABSENSI</li>
            <li class="nav-item">
                <a href="{{ route('guru.absensi.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-check-square"></i>
                    <p>
                        Absensi
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('guru.jadwal-guru.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-clock"></i>
                    <p>
                        Jadwal Mengajar
                    </p>
                </a>
            </li>
        @endif

        @if (auth()->user()->hasRole('siswa'))
            <li class="nav-item">
                <a href="{{ route('siswa.jadwal.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-calendar-alt"></i>
                    <p>
                        Jadwal Pelajaran
                    </p>
                </a>
            </li>
        @endif

        <li class="nav-item">
            <a href="{{ route('logout') }}" class="nav-link"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>
                    Logout
                </p>
            </a>
        </li>
        <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
            @csrf
        </form>

    </ul>
</nav>
