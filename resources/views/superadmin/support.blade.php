@extends('layouts.app')

@section('title', 'Support - SIMANJA')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header" style="background-color: #1565c0; color: white;">
                <strong>Halaman Support</strong>
            </div>
            <div class="card-body">
                <!-- Support Information Section -->
                <div class="row">
                    <div class="col-md-6 text-center">
                        <img src="/support1.png" class="img-fluid mx-auto" alt="Support Image">
                        <h4 class="mt-4">Butuh bantuan Penggunaan SIMANJA?</h4>
                        <div class="d-flex justify-content-center mt-3">
                            <!-- WhatsApp link with icon -->
                            <a href="https://wa.me/6281229429025" class="d-flex align-items-center me-3" target="_blank">
                                <i class="fas fa-phone" style="font-size: 24px;"></i> <span class="ms-2">+6281229429025</span>
                            </a>
                            <!-- Email link with icon -->
                            <a href="mailto:kyuushaxyz@gmail.com" class="d-flex align-items-center" target="_blank">
                                <i class="fas fa-envelope" style="font-size: 24px;"></i> <span class="ms-2">kyuushaxyz@gmail.com</span>
                            </a>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Video Tutorial Card -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <img src="\support2.png" class="img-fluid" alt="Video Tutorial" width="75" height="75">
                                    </div>
                                    <div class="col">
                                        <h5 class="card-title"><strong>Video Tutorial</strong></h5>
                                        <p class="card-text">Tonton video tutorial penggunaan SIMANJA untuk mempelajari fitur-fitur utama.</p>
                                    </div>
                                </div>
                                <br/>
                                <a href="#" class="btn btn-primary">Lihat Video</a>
                            </div>
                        </div>

                        <!-- Documentation Card -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <img src="\support3.png" class="img-fluid" alt="Documentation" width="75" height="75">
                                    </div>
                                    <div class="col">
                                        <h5 class="card-title"><strong>Dokumentasi Penggunaan SIMANJA</strong></h5>
                                        <p class="card-text">Baca dokumentasi lengkap mengenai cara penggunaan SIMANJA untuk memaksimalkan penggunaan sistem.</p>
                                    </div>
                                </div>
                                <br/>
                                <a href="#" class="btn btn-primary">Baca Dokumentasi</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="accordion mt-4" id="accordionFAQ">
                    <h4>Frequently Asked Questions</h4>

                    <!-- FAQ Item 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Bagaimana cara mengganti password akun SIMANJA?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body">
                                Untuk mengganti password akun, klik "Lupa Password?" di halaman login, masukkan email akun Anda, dan ikuti instruksi yang diberikan.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Bagaimana cara membuat pekerjaan untuk pegawai?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body">
                                Klik "Tambah Pekerjaan" di halaman progress tim ketua, pilih jenis pekerjaan dan target waktu, lalu pilih pegawai yang bertugas.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Bagaimana cara mengekspor pekerjaan berdasarkan bulan yang diinginkan?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body">
                                Pilih bulan dan tahun yang diinginkan di halaman progress tim ketua, klik tombol "Filter", kemudian pilih "Export" untuk mengunduh data.
                            </div>
                        </div>
                    </div>

                    <!-- Add more FAQ items as needed -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Add FontAwesome CDN here -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
@endpush
