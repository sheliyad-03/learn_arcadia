<section class="about-area tg-motion-effects section-py-120">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6 col-md-9">
                <div class="about__images">
                    <img src="{{ asset($aboutSection?->global_content?->image) }}" alt="img" class="main-img">
                    <img src="{{ asset('frontend/img/others/about_shape.svg') }}" alt="img" class="shape alltuchtopdown">
                    <a href="{{ $aboutSection?->global_content?->video_url}}" class="popup-video" aria-label="Watch introductory video">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="28" viewBox="0 0 22 28"
                            fill="none">
                            <path
                                d="M0.19043 26.3132V1.69421C0.190288 1.40603 0.245303 1.12259 0.350273 0.870694C0.455242 0.6188 0.606687 0.406797 0.79027 0.254768C0.973854 0.10274 1.1835 0.0157243 1.39936 0.00193865C1.61521 -0.011847 1.83014 0.0480663 2.02378 0.176003L20.4856 12.3292C20.6973 12.4694 20.8754 12.6856 20.9999 12.9535C21.1245 13.2214 21.1904 13.5304 21.1904 13.8456C21.1904 14.1608 21.1245 14.4697 20.9999 14.7376C20.8754 15.0055 20.6973 15.2217 20.4856 15.3619L2.02378 27.824C1.83056 27.9517 1.61615 28.0116 1.40076 27.9981C1.18536 27.9847 0.97607 27.8983 0.792638 27.7472C0.609205 27.596 0.457661 27.385 0.352299 27.1342C0.246938 26.8833 0.191236 26.6008 0.19043 26.3132Z"
                                fill="currentcolor" />
                        </svg>
                    </a>
                    <div class="about__enrolled" data-aos="fade-right" data-aos-delay="200">
                        <p class="title"><span>{{ $hero?->content?->total_student }}</span> {{ __('Enrolled Students') }}</p>
                        <img src="{{ asset($hero?->global_content?->enroll_students_image) }}" alt="img">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about__content">
                    <div class="section__title">
                        <span class="sub-title">{{ $aboutSection?->content?->short_title }}</span>
                        <h2 class="title">
                           {!! clean(processText($aboutSection?->content?->title)) !!} 
                        </h2>
                    </div>
                    
                    {!! clean(processText($aboutSection?->content?->description)) !!}
  
                    <div class="tg-button-wrap">
                        <a href="{{ $aboutSection?->global_content?->button_url }}" class="btn arrow-btn">{{ $aboutSection?->content?->button_text }} <img
                                src="{{ asset('frontend/img/icons/right_arrow.svg') }}" alt="img" class="injectable"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </section>