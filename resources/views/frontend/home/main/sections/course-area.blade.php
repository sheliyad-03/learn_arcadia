<section class="courses-area section-pt-120 section-pb-90"
    data-background="{{ asset('frontend/img/bg/courses_bg.jpg') }}">
    <div class="container">
        <div class="section__title-wrap">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <div class="section__title text-center mb-40">
                        <span class="sub-title">{{ __('Top Class Courses') }}</span>
                        <h2 class="title">{{ __('Explore Our Worlds Featured Courses') }}</h2>
                        <p class="desc">{{ __('Check out the most demanding courses right now') }}</p>
                    </div>
                    <div class="courses__nav">
                        <ul class="nav nav-tabs" id="courseTab" role="tablist">
                            @php
                                $allCoursesIds = json_decode(
                                    $featuredCourse?->all_category_ids ? $featuredCourse->all_category_ids : '[]',
                                );
                                $allCourses = App\Models\Course::with('favoriteBy','category.translation', 'instructor:id,name')
                                    ->whereIn('id', $allCoursesIds)
                                    ->withCount([
                                        'reviews as avg_rating' => function ($query) {
                                            $query->select(DB::raw('coalesce(avg(rating), 0)'));
                                        },
                                    ])
                                    ->withCount('enrollments')
                                    ->get();
                            @endphp
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab"
                                    data-bs-target="#all-tab-pane" type="button" role="tab"
                                    aria-controls="all-tab-pane" aria-selected="true">
                                    {{ __('All Courses') }}
                                </button>
                            </li>
                            @if ($featuredCourse?->category_one_status == 1)
                                <li class="nav-item" role="presentation">
                                    @php
                                        $categoryOne = Modules\Course\app\Models\CourseCategory::with(['translation'])
                                            ->where('id', $featuredCourse->category_one)
                                            ->first();
                                        $categoryOneIds = json_decode($featuredCourse->category_one_ids);
                                        $categoryOneCourses = App\Models\Course::with(
                                            'favoriteBy','category.translation',
                                            'instructor:id,name',
                                        )
                                            ->whereIn('id', $categoryOneIds)
                                            ->withCount([
                                                'reviews as avg_rating' => function ($query) {
                                                    $query->select(DB::raw('coalesce(avg(rating), 0)'));
                                                },
                                            ])
                                            ->withCount('enrollments')
                                            ->get();
                                    @endphp
                                    <button class="nav-link" id="design-tab" data-bs-toggle="tab"
                                        data-bs-target="#design-tab-pane" type="button" role="tab"
                                        aria-controls="design-tab-pane" aria-selected="false">
                                        {{ $categoryOne?->name }}
                                    </button>
                                </li>
                            @endif
                            @if ($featuredCourse?->category_two_status == 1)
                                <li class="nav-item" role="presentation">
                                    @php
                                        $categoryTwo = Modules\Course\app\Models\CourseCategory::with(['translation'])
                                            ->where('id', $featuredCourse->category_two)
                                            ->first();
                                        $categoryTwoIds = json_decode($featuredCourse->category_two_ids);
                                        $categoryTwoCourses = App\Models\Course::with(
                                            'favoriteBy','category.translation',
                                            'instructor:id,name',
                                        )
                                            ->whereIn('id', $categoryTwoIds)
                                            ->withCount([
                                                'reviews as avg_rating' => function ($query) {
                                                    $query->select(DB::raw('coalesce(avg(rating), 0)'));
                                                },
                                            ])
                                            ->withCount('enrollments')
                                            ->get();
                                    @endphp

                                    <button class="nav-link" id="business-tab" data-bs-toggle="tab"
                                        data-bs-target="#business-tab-pane" type="button" role="tab"
                                        aria-controls="business-tab-pane" aria-selected="false">
                                        {{ $categoryTwo?->name }}
                                    </button>
                                </li>
                            @endif

                            @if ($featuredCourse?->category_three_status == 1)
                                <li class="nav-item" role="presentation">
                                    @php
                                        $categoryThree = Modules\Course\app\Models\CourseCategory::with(['translation'])
                                            ->where('id', $featuredCourse->category_three)
                                            ->first();
                                        $categoryThreeIds = json_decode($featuredCourse->category_three_ids);
                                        $categoryThreeCourses = App\Models\Course::with(
                                            'favoriteBy','category.translation',
                                            'instructor:id,name',
                                        )
                                            ->whereIn('id', $categoryThreeIds)
                                            ->withCount([
                                                'reviews as avg_rating' => function ($query) {
                                                    $query->select(DB::raw('coalesce(avg(rating), 0)'));
                                                },
                                            ])
                                            ->withCount('enrollments')
                                            ->get();
                                    @endphp
                                    <button class="nav-link" id="development-tab" data-bs-toggle="tab"
                                        data-bs-target="#development-tab-pane" type="button" role="tab"
                                        aria-controls="development-tab-pane" aria-selected="false">
                                        {{ $categoryThree?->name }}
                                    </button>
                                </li>
                            @endif

                            @if ($featuredCourse?->category_four_status == 1)
                                <li class="nav-item" role="presentation">
                                    @php
                                        $categoryFour = Modules\Course\app\Models\CourseCategory::with(['translation'])
                                            ->where('id', $featuredCourse->category_four)
                                            ->first();
                                        $categoryFourIds = json_decode($featuredCourse->category_four_ids);
                                        $categoryFourCourses = App\Models\Course::with(
                                            'favoriteBy','category.translation',
                                            'instructor:id,name',
                                        )
                                            ->whereIn('id', $categoryFourIds)
                                            ->withCount([
                                                'reviews as avg_rating' => function ($query) {
                                                    $query->select(DB::raw('coalesce(avg(rating), 0)'));
                                                },
                                            ])
                                            ->withCount('enrollments')
                                            ->get();
                                    @endphp
                                    <button class="nav-link" id="categoryFour-tab" data-bs-toggle="tab"
                                        data-bs-target="#categoryFour-tab-pane" type="button" role="tab"
                                        aria-controls="categoryFour-tab-pane" aria-selected="false">
                                        {{ $categoryFour?->name }}
                                    </button>
                                </li>
                            @endif

                            @if ($featuredCourse?->category_five_status == 1)
                                <li class="nav-item" role="presentation">
                                    @php
                                        $categoryFive = Modules\Course\app\Models\CourseCategory::with(['translation'])
                                            ->where('id', $featuredCourse->category_five)
                                            ->first();
                                        $categoryFiveIds = json_decode($featuredCourse->category_five_ids);
                                        $categoryFiveCourses = App\Models\Course::with(
                                            'favoriteBy','category.translation',
                                            'instructor:id,name',
                                        )
                                            ->whereIn('id', $categoryFiveIds)
                                            ->withCount([
                                                'reviews as avg_rating' => function ($query) {
                                                    $query->select(DB::raw('coalesce(avg(rating), 0)'));
                                                },
                                            ])
                                            ->withCount('enrollments')
                                            ->get();
                                    @endphp
                                    <button class="nav-link" id="development-tab" data-bs-toggle="tab"
                                        data-bs-target="#categoryFive-tab-pane" type="button" role="tab"
                                        aria-controls="development-tab-pane" aria-selected="false">
                                        {{ $categoryFive?->name }}
                                    </button>
                                </li>
                            @endif

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content" id="courseTabContent">
            <div class="tab-pane fade show active" id="all-tab-pane" role="tabpanel" aria-labelledby="all-tab"
                tabindex="0">
                <div class="swiper courses-swiper-active">
                    <div class="swiper-wrapper">
                        @foreach ($allCourses ?? [] as $course)
                            <div class="swiper-slide">
                                <div class="courses__item shine__animate-item">
                                    <div class="courses__item-thumb">
                                        <a href="{{ route('course.show', $course->slug) }}"
                                            class="shine__animate-link">
                                            <img src="{{ asset($course->thumbnail) }}" alt="img">
                                        </a>
                                        <a href="javascript:;" class="wsus-wishlist-btn common-white courses__wishlist-two"  aria-label="WishList"
                                            data-slug="{{ $course?->slug }}">
                                            <i class="{{ $course?->favorite_by_client ? 'fas' : 'far' }} fa-heart"></i>
                                        </a>
                                    </div>
                                    <div class="courses__item-content">
                                        <ul class="courses__item-meta list-wrap">
                                            <li class="courses__item-tag">
                                                <a
                                                    href="{{ route('courses', ['category' => $course->category->id]) }}">{{ $course->category?->name }}</a>
                                            </li>
                                            <li class="avg-rating"><i class="fas fa-star"></i>
                                                {{ number_format($course->avg_rating, 1) ?? 0 }}
                                            </li>
                                        </ul>
                                        <h3 class="title"><a
                                                href="{{ route('course.show', $course->slug) }}">{{ truncate($course->title, 50) }}</a>
                                        </h3>
                                        <p class="author">{{ __('By') }} <a
                                                href="{{ route('instructor-details', ['id' => $course->instructor->id, 'slug' => Str::slug($course->instructor->name)]) }}">{{ $course->instructor->name }}</a>
                                        </p>
                                        <div class="courses__item-bottom">
                                            @if (in_array($course->id, session('enrollments') ?? []))
                                                <div class="button">
                                                    <a href="{{ route('student.enrolled-courses') }}"
                                                        class="already-enrolled-btn" data-id="">
                                                        <span class="text">{{ __('Enrolled') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @elseif ($course->enrollments_count >= $course->capacity && $course->capacity != null)
                                                <div class="button">
                                                    <a href="javascript:;" class=""
                                                        data-id="{{ $course->id }}">
                                                        <span class="text">{{ __('Booked') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="button">
                                                    <a href="javascript:;" class="add-to-cart"
                                                        data-id="{{ $course->id }}">
                                                        <span class="text">{{ __('Add To Cart') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @endif

                                            @if ($course->price == 0)
                                                <h4 class="price">{{ __('Free') }}</h4>
                                            @elseif ($course->price > 0 && $course->discount > 0)
                                                <h4 class="price">{{ currency($course->discount) }}</h4>
                                            @else
                                                <h4 class="price">{{ currency($course->price) }}</h4>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
                <div class="courses__nav">
                    <div class="courses-button-prev"><i class="flaticon-arrow-right"></i></div>
                    <div class="courses-button-next"><i class="flaticon-arrow-right"></i></div>
                </div>
            </div>

            <div class="tab-pane fade" id="design-tab-pane" role="tabpanel" aria-labelledby="design-tab-pane"
                tabindex="0">
                <div class="swiper courses-swiper-active">
                    <div class="swiper-wrapper">
                        @foreach ($categoryOneCourses ?? [] as $course)
                            <div class="swiper-slide">
                                <div class="courses__item shine__animate-item">
                                    <div class="courses__item-thumb">
                                        <a href="{{ route('course.show', $course->slug) }}"
                                            class="shine__animate-link">
                                            <img src="{{ asset($course->thumbnail) }}" alt="img">
                                        </a>
                                        <a href="javascript:;" class="wsus-wishlist-btn common-white courses__wishlist-two"  aria-label="WishList"
                                            data-slug="{{ $course?->slug }}">
                                            <i class="{{ $course?->favorite_by_client ? 'fas' : 'far' }} fa-heart"></i>
                                        </a>
                                    </div>
                                    <div class="courses__item-content">
                                        <ul class="courses__item-meta list-wrap">
                                            <li class="courses__item-tag">
                                                <a
                                                    href="{{ route('courses', ['category' => $course->category->id]) }}">{{ $course->category?->name }}</a>
                                            </li>
                                            <li class="avg-rating"><i class="fas fa-star"></i>
                                                {{ number_format($course->avg_rating, 1) ?? 0 }}
                                            </li>
                                        </ul>
                                        <h3 class="title"><a
                                                href="{{ route('course.show', $course->slug) }}">{{ truncate($course->title, 50) }}</a>
                                        </h3>
                                        <p class="author">{{ __('By') }} <a
                                                href="{{ route('instructor-details', ['id' => $course->instructor->id, 'slug' => Str::slug($course->instructor->name)]) }}">{{ $course->instructor->name }}</a>
                                        </p>
                                        <div class="courses__item-bottom">
                                            @if (in_array($course->id, session('enrollments') ?? []))
                                                <div class="button">
                                                    <a href="{{ route('student.enrolled-courses') }}"
                                                        class="already-enrolled-btn" data-id="">
                                                        <span class="text">{{ __('Enrolled') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @elseif ($course->enrollments_count >= $course->capacity && $course->capacity != null)
                                                <div class="button">
                                                    <a href="javascript:;" class=""
                                                        data-id="{{ $course->id }}">
                                                        <span class="text">{{ __('Booked') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="button">
                                                    <a href="javascript:;" class="add-to-cart"
                                                        data-id="{{ $course->id }}">
                                                        <span class="text">{{ __('Add To Cart') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @endif
                                            @if ($course->price == 0)
                                                <h4 class="price">{{ __('Free') }}</h4>
                                            @elseif ($course->price > 0 && $course->discount > 0)
                                                <h4 class="price">{{ currency($course->discount) }}</h4>
                                            @else
                                                <h4 class="price">{{ currency($course->price) }}</h4>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
                <div class="courses__nav">
                    <div class="courses-button-prev"><i class="flaticon-arrow-right"></i></div>
                    <div class="courses-button-next"><i class="flaticon-arrow-right"></i></div>
                </div>
            </div>

            <div class="tab-pane fade" id="business-tab-pane" role="tabpanel" aria-labelledby="business-tab-pane"
                tabindex="0">
                <div class="swiper courses-swiper-active">
                    <div class="swiper-wrapper">
                        @foreach ($categoryTwoCourses ?? [] as $course)
                            <div class="swiper-slide">
                                <div class="courses__item shine__animate-item">
                                    <div class="courses__item-thumb">
                                        <a href="{{ route('course.show', $course->slug) }}"
                                            class="shine__animate-link">
                                            <img src="{{ asset($course->thumbnail) }}" alt="img">
                                        </a>
                                        <a href="javascript:;" class="wsus-wishlist-btn common-white courses__wishlist-two"  aria-label="WishList"
                                            data-slug="{{ $course?->slug }}">
                                            <i class="{{ $course?->favorite_by_client ? 'fas' : 'far' }} fa-heart"></i>
                                        </a>
                                    </div>
                                    <div class="courses__item-content">
                                        <ul class="courses__item-meta list-wrap">
                                            <li class="courses__item-tag">
                                                <a
                                                    href="{{ route('courses', ['category' => $course->category->id]) }}">{{ $course->category?->name }}</a>
                                            </li>
                                            <li class="avg-rating"><i class="fas fa-star"></i>
                                                {{ number_format($course->avg_rating, 1) ?? 0 }}
                                            </li>
                                        </ul>
                                        <h3 class="title"><a
                                                href="{{ route('course.show', $course->slug) }}">{{ truncate($course->title, 50) }}</a>
                                        </h3>
                                        <p class="author">{{ __('By') }} <a
                                                href="{{ route('instructor-details', ['id' => $course->instructor->id, 'slug' => Str::slug($course->instructor->name)]) }}">{{ $course->instructor->name }}</a>
                                        </p>
                                        <div class="courses__item-bottom">
                                            @if (in_array($course->id, session('enrollments') ?? []))
                                                <div class="button">
                                                    <a href="{{ route('student.enrolled-courses') }}"
                                                        class="already-enrolled-btn" data-id="">
                                                        <span class="text">{{ __('Enrolled') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @elseif ($course->enrollments_count >= $course->capacity && $course->capacity != null)
                                                <div class="button">
                                                    <a href="javascript:;" class=""
                                                        data-id="{{ $course->id }}">
                                                        <span class="text">{{ __('Booked') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="button">
                                                    <a href="javascript:;" class="add-to-cart"
                                                        data-id="{{ $course->id }}">
                                                        <span class="text">{{ __('Add To Cart') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @endif
                                            @if ($course->price == 0)
                                                <h4 class="price">{{ __('Free') }}</h4>
                                            @elseif ($course->price > 0 && $course->discount > 0)
                                                <h4 class="price">{{ currency($course->discount) }}</h4>
                                            @else
                                                <h4 class="price">{{ currency($course->price) }}</h4>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
                <div class="courses__nav">
                    <div class="courses-button-prev"><i class="flaticon-arrow-right"></i></div>
                    <div class="courses-button-next"><i class="flaticon-arrow-right"></i></div>
                </div>
            </div>

            <div class="tab-pane fade" id="development-tab-pane" role="tabpanel"
                aria-labelledby="development-tab-pane" tabindex="0">
                <div class="swiper courses-swiper-active">
                    <div class="swiper-wrapper">
                        @foreach ($categoryThreeCourses ?? [] as $course)
                            <div class="swiper-slide">
                                <div class="courses__item shine__animate-item">
                                    <div class="courses__item-thumb">
                                        <a href="{{ route('course.show', $course->slug) }}"
                                            class="shine__animate-link">
                                            <img src="{{ asset($course->thumbnail) }}" alt="img">
                                        </a>
                                        <a href="javascript:;" class="wsus-wishlist-btn common-white courses__wishlist-two"  aria-label="WishList"
                                            data-slug="{{ $course?->slug }}">
                                            <i class="{{ $course?->favorite_by_client ? 'fas' : 'far' }} fa-heart"></i>
                                        </a>
                                    </div>
                                    <div class="courses__item-content">
                                        <ul class="courses__item-meta list-wrap">
                                            <li class="courses__item-tag">
                                                <a
                                                    href="{{ route('courses', ['category' => $course->category->id]) }}">{{ $course->category?->name }}</a>
                                            </li>
                                            <li class="avg-rating"><i class="fas fa-star"></i>
                                                {{ number_format($course->avg_rating, 1) ?? 0 }}
                                            </li>
                                        </ul>
                                        <h3 class="title"><a
                                                href="{{ route('course.show', $course->slug) }}">{{ truncate($course->title, 50) }}</a>
                                        </h3>
                                        <p class="author">{{ __('By') }} <a
                                                href="{{ route('instructor-details', ['id' => $course->instructor->id, 'slug' => Str::slug($course->instructor->name)]) }}">{{ $course->instructor->name }}</a>
                                        </p>
                                        <div class="courses__item-bottom">
                                            @if (in_array($course->id, session('enrollments') ?? []))
                                                <div class="button">
                                                    <a href="{{ route('student.enrolled-courses') }}"
                                                        class="already-enrolled-btn" data-id="">
                                                        <span class="text">{{ __('Enrolled') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @elseif ($course->enrollments_count >= $course->capacity && $course->capacity != null)
                                                <div class="button">
                                                    <a href="javascript:;" class=""
                                                        data-id="{{ $course->id }}">
                                                        <span class="text">{{ __('Booked') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="button">
                                                    <a href="javascript:;" class="add-to-cart"
                                                        data-id="{{ $course->id }}">
                                                        <span class="text">{{ __('Add To Cart') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @endif
                                            @if ($course->price == 0)
                                                <h4 class="price">{{ __('Free') }}</h4>
                                            @elseif ($course->price > 0 && $course->discount > 0)
                                                <h4 class="price">{{ currency($course->discount) }}</h4>
                                            @else
                                                <h4 class="price">{{ currency($course->price) }}</h4>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
                <div class="courses__nav">
                    <div class="courses-button-prev"><i class="flaticon-arrow-right"></i></div>
                    <div class="courses-button-next"><i class="flaticon-arrow-right"></i></div>
                </div>
            </div>

            <div class="tab-pane fade" id="categoryFour-tab-pane" role="tabpanel"
                aria-labelledby="categoryFour-tab-pane" tabindex="0">
                <div class="swiper courses-swiper-active">
                    <div class="swiper-wrapper">
                        @foreach ($categoryFourCourses ?? [] as $course)
                            <div class="swiper-slide">
                                <div class="courses__item shine__animate-item">
                                    <div class="courses__item-thumb">
                                        <a href="{{ route('course.show', $course->slug) }}"
                                            class="shine__animate-link">
                                            <img src="{{ asset($course->thumbnail) }}" alt="img">
                                        </a>
                                        <a href="javascript:;" class="wsus-wishlist-btn common-white courses__wishlist-two"  aria-label="WishList"
                                            data-slug="{{ $course?->slug }}">
                                            <i class="{{ $course?->favorite_by_client ? 'fas' : 'far' }} fa-heart"></i>
                                        </a>
                                    </div>
                                    <div class="courses__item-content">
                                        <ul class="courses__item-meta list-wrap">
                                            <li class="courses__item-tag">
                                                <a
                                                    href="{{ route('courses', ['category' => $course->category->id]) }}">{{ $course->category?->name }}</a>
                                            </li>
                                            <li class="avg-rating"><i class="fas fa-star"></i>
                                                {{ number_format($course->avg_rating, 1) ?? 0 }}
                                            </li>
                                        </ul>
                                        <h3 class="title"><a
                                                href="{{ route('course.show', $course->slug) }}">{{ truncate($course->title, 50) }}</a>
                                        </h3>
                                        <p class="author">{{ __('By') }} <a
                                                href="{{ route('instructor-details', ['id' => $course->instructor->id, 'slug' => Str::slug($course->instructor->name)]) }}">{{ $course->instructor->name }}</a>
                                        </p>
                                        <div class="courses__item-bottom">
                                            @if (in_array($course->id, session('enrollments') ?? []))
                                                <div class="button">
                                                    <a href="{{ route('student.enrolled-courses') }}"
                                                        class="already-enrolled-btn" data-id="">
                                                        <span class="text">{{ __('Enrolled') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @elseif ($course->enrollments_count >= $course->capacity && $course->capacity != null)
                                                <div class="button">
                                                    <a href="javascript:;" class=""
                                                        data-id="{{ $course->id }}">
                                                        <span class="text">{{ __('Booked') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="button">
                                                    <a href="javascript:;" class="add-to-cart"
                                                        data-id="{{ $course->id }}">
                                                        <span class="text">{{ __('Add To Cart') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @endif
                                            @if ($course->price == 0)
                                                <h4 class="price">{{ __('Free') }}</h4>
                                            @elseif ($course->price > 0 && $course->discount > 0)
                                                <h4 class="price">{{ currency($course->discount) }}</h4>
                                            @else
                                                <h4 class="price">{{ currency($course->price) }}</h4>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
                <div class="courses__nav">
                    <div class="courses-button-prev"><i class="flaticon-arrow-right"></i></div>
                    <div class="courses-button-next"><i class="flaticon-arrow-right"></i></div>
                </div>
            </div>

            <div class="tab-pane fade" id="categoryFive-tab-pane" role="tabpanel"
                aria-labelledby="categoryFive-tab-pane" tabindex="0">
                <div class="swiper courses-swiper-active">
                    <div class="swiper-wrapper">
                        @foreach ($categoryFiveCourses ?? [] as $course)
                            <div class="swiper-slide">
                                <div class="courses__item shine__animate-item">
                                    <div class="courses__item-thumb">
                                        <a href="{{ route('course.show', $course->slug) }}"
                                            class="shine__animate-link">
                                            <img src="{{ asset($course->thumbnail) }}" alt="img">
                                        </a>
                                        <a href="javascript:;" class="wsus-wishlist-btn common-white courses__wishlist-two"  aria-label="WishList"
                                            data-slug="{{ $course?->slug }}">
                                            <i class="{{ $course?->favorite_by_client ? 'fas' : 'far' }} fa-heart"></i>
                                        </a>
                                    </div>
                                    <div class="courses__item-content">
                                        <ul class="courses__item-meta list-wrap">
                                            <li class="courses__item-tag">
                                                <a
                                                    href="{{ route('courses', ['category' => $course->category->id]) }}">{{ $course->category?->name }}</a>
                                            </li>
                                            <li class="avg-rating"><i class="fas fa-star"></i>
                                                {{ number_format($course->avg_rating, 1) ?? 0 }}
                                            </li>
                                        </ul>
                                        <h3 class="title"><a
                                                href="{{ route('course.show', $course->slug) }}">{{ truncate($course->title, 50) }}</a>
                                        </h3>
                                        <p class="author">{{ __('By') }} <a
                                                href="{{ route('instructor-details', ['id' => $course->instructor->id, 'slug' => Str::slug($course->instructor->name)]) }}">{{ $course->instructor->name }}</a>
                                        </p>
                                        <div class="courses__item-bottom">
                                            @if (in_array($course->id, session('enrollments') ?? []))
                                                <div class="button">
                                                    <a href="{{ route('student.enrolled-courses') }}"
                                                        class="already-enrolled-btn" data-id="">
                                                        <span class="text">{{ __('Enrolled') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @elseif ($course->enrollments_count >= $course->capacity && $course->capacity != null)
                                                <div class="button">
                                                    <a href="javascript:;" class=""
                                                        data-id="{{ $course->id }}">
                                                        <span class="text">{{ __('Booked') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="button">
                                                    <a href="javascript:;" class="add-to-cart"
                                                        data-id="{{ $course->id }}">
                                                        <span class="text">{{ __('Add To Cart') }}</span>
                                                        <i class="flaticon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @endif
                                            @if ($course->price == 0)
                                                <h4 class="price">{{ __('Free') }}</h4>
                                            @elseif ($course->price > 0 && $course->discount > 0)
                                                <h4 class="price">{{ currency($course->discount) }}</h4>
                                            @else
                                                <h4 class="price">{{ currency($course->price) }}</h4>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
                <div class="courses__nav">
                    <div class="courses-button-prev"><i class="flaticon-arrow-right"></i></div>
                    <div class="courses-button-next"><i class="flaticon-arrow-right"></i></div>
                </div>
            </div>

        </div>
    </div>
</section>
