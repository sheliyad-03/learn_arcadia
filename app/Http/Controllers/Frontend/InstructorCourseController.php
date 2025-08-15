<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\CourseChapter;
use App\Models\CourseSelectedLevel;
use App\Rules\ValidateDiscountRule;
use App\Http\Controllers\Controller;
use App\Models\CourseSelectedLanguage;
use App\Models\CoursePartnerInstructor;
use Illuminate\Support\Facades\Session;
use Modules\Order\app\Models\OrderItem;
use Modules\Course\app\Models\CourseLevel;
use Modules\Course\app\Models\CourseCategory;
use Modules\Course\app\Models\CourseLanguage;
use Modules\Course\app\Models\CourseDeleteRequest;

class InstructorCourseController extends Controller {
    public function index(): View {
        $courses = Course::where('instructor_id', userAuth()->id)->paginate(10);
        return view('frontend.instructor-dashboard.course.index', compact('courses'));
    }

    public function create() {
        return view('frontend.instructor-dashboard.course.create');
    }

    public function editView(string $id) {
        Session::put('course_create', $id);
        $course = Course::where('instructor_id', userAuth()->id)->findOrFail($id);
        $editMode = true;
        return view('frontend.instructor-dashboard.course.create', compact('course', 'editMode'));
    }

    public function store(Request $request) {
        $rules = [
            'title'             => ['required', 'max:255'],
            'seo_description'   => ['nullable', 'string', 'max:255'],
            'thumbnail'         => ['required', 'max:255'],
            'demo_video_source' => ['nullable', 'string'],
            'price'             => ['required', 'numeric', 'min:0'],
            'discount_price'    => ['nullable', 'numeric', new ValidateDiscountRule()],
            'description'       => ['required', 'string', 'max:5000'],
        ];
        $messages = [
            'title.required'           => __('Title is required'),
            'title.max'                => __('Title must be less than 255 characters long'),
            'seo_description.string'   => __('Seo description must be a string'),
            'seo_description.max'      => __('Seo description must be less than 255 characters long'),
            'thumbnail.required'       => __('Thumbnail is required'),
            'thumbnail.max'            => __('Thumbnail must be less than 255 characters long'),
            'demo_video_source.string' => __('Demo video source must be a string'),
            'path.string'              => __('Path must be a string'),
            'price.required'           => __('Price is required'),
            'price.numeric'            => __('Price must be a number'),
            'price.min'                => __('Price must be greater than or equal to 0'),
            'discount.numeric'         => __('Discount must be a number'),
            'description.required'     => __('Description is required'),
            'description.string'       => __('Description must be a string'),
            'description.max'          => __('Description must be less than 5000 characters long'),
            'instructor.required'      => __('Instructor is required'),
            'instructor.numeric'       => __('Instructor must be a number'),
        ];

        $request->validate($rules, $messages);
        if ($request->edit_mode == 1) {
            $course = Course::where('instructor_id', userAuth()->id)->findOrFail($request->id);
        } else {
            $course = new Course();
            $slug = generateUniqueSlug(Course::class, $request->title);
            $course->slug = $slug;
        }

        $course->title = $request->title;
        $course->instructor_id = auth('web')->user()->id;
        $course->seo_description = $request->seo_description;
        $course->thumbnail = $request->thumbnail;
        $course->demo_video_storage = $request->demo_video_storage;
        $course->demo_video_source = $request->demo_video_storage == 'upload' ? $request->upload_path : $request->external_path;
        $course->price = $request->price;
        $course->discount = $request->discount_price;
        $course->description = $request->description;
        $course->save();

        // save course id in session
        Session::put('course_create', $course->id);

        return response()->json([
            'status'   => 'success',
            'message'  => __('Updated successfully'),
            'redirect' => route('instructor.courses.edit', ['id' => $course->id, 'step' => $request->next_step]),
        ]);
    }

    public function edit(Request $request) {
        if (!Session::get('course_create')) {
            return redirect(route('instructor.courses.create'));
        }

        switch (request('step')) {
        case '1':
            $course = Course::where('instructor_id', userAuth()->id)->findOrFail($request->id);
            $editMode = true;
            return view('frontend.instructor-dashboard.course.create', compact('course', 'editMode'));
            break;
        case '2':
            $courseId = request('id');
            $categories = CourseCategory::where('status', 1)->get();
            $course = Course::where('instructor_id', userAuth()->id)->findOrFail($courseId);
            $levels = CourseLevel::with(['translation'])->where('status', 1)->get();
            $category = CourseCategory::find($course->category_id);
            $languages = CourseLanguage::where('status', 1)->get();
            return view('frontend.instructor-dashboard.course.more-information', compact(
                'categories',
                'courseId',
                'course',
                'levels',
                'category',
                'languages'
            ));
            break;
        case '3':
            $chapters = CourseChapter::with(['chapterItems'])->where(['course_id' => $request->id, 'status' => 'active'])->orderBy('order')->get();
            return view('frontend.instructor-dashboard.course.course-content', compact('chapters'));
            break;
        case '4':
            $course = Course::where('instructor_id', userAuth()->id)->findOrFail($request->id);

            $year = $request->input('year', Carbon::now()->year);

            // Get start and end of the year
            $start = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $end = $start->copy()->endOfYear();

            $orderItems = OrderItem::where('course_id', $course->id)->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            })->selectRaw('YEAR(created_at) as year,
                MONTH(created_at) as month,
                SUM(price) as total_price,
                AVG(commission_rate) as commission_rate')->whereBetween('created_at', [$start, $end])->groupBy('year', 'month')->orderBy('month', 'asc')->get();

            $monthlySales = array_fill(1, 12, 0);
            $commissionMonthly = array_fill(1, 12, 0);
            $netMonthly = array_fill(1, 12, 0);

            foreach ($orderItems as $item) {
                $totalAmount = $item->total_price;
                $commissionAmount = $totalAmount * ($item->commission_rate / 100);
                $netEarnings = $totalAmount - $commissionAmount;
                $monthlySales[$item->month] = $totalAmount;
                $commissionMonthly[$item->month] = $commissionAmount;
                $netMonthly[$item->month] = $netEarnings;
            }

            $oldestYear = Carbon::parse(OrderItem::orderBy('created_at', 'asc')->first()?->created_at)->year ?? Carbon::now()->year;
            $latestYear = Carbon::parse(OrderItem::orderBy('created_at', 'desc')->first()?->created_at)->year ?? Carbon::now()->year;
            $month_labels = array_map(fn($month) => Carbon::create()->month($month)->format('M'), array_keys($monthlySales));

            $commission_monthly_data = array_values($commissionMonthly);
            $net_monthly_data = array_values($netMonthly);
            $order_monthly_data = array_values($monthlySales);

            $total_chapter_items = $course->chapterItems()->count();
            $enrollments = $course->enrollments;
            $progress_ranges = array_fill_keys(['0-10', '10-20', '20-30', '30-40', '40-50', '50-60', '60-70', '70-80', '80-90', '90-100'], 0);

            foreach ($enrollments as $enrollment) {
                $percentage = min(($total_chapter_items > 0 ? $course->progresses()->where('user_id', $enrollment->user_id)->count() / $total_chapter_items * 100 : 0), 100);

                $range = $percentage == 100 ? '90-100' : floor($percentage / 10) * 10 . '-' . (floor($percentage / 10) * 10 + 10);
                $progress_ranges[$range]++;
            }
            $total_enrollments = $enrollments->count();

            return view('frontend.instructor-dashboard.course.analytics', compact('course', 'progress_ranges', 'total_enrollments', 'oldestYear', 'latestYear', 'month_labels', 'commission_monthly_data', 'net_monthly_data', 'order_monthly_data'));
            break;
        case '5':
            $courseId = request('id');
            $course = Course::where('instructor_id', userAuth()->id)->findOrFail($courseId);
            return view('frontend.instructor-dashboard.course.finish', compact('course'));
            break;
        default:
            break;
        }
    }

    public function update(Request $request) {
        switch ($request->step) {
        case '2':
            $request->validate([
                'course_duration' => ['required', 'numeric', 'min:0'],
                'category'        => ['required'],
            ]);
            $this->storeMoreInfo($request);
            return response()->json([
                'status'   => 'success',
                'message'  => __('Updated Successfully'),
                'redirect' => route('instructor.courses.edit', ['id' => Session::get('course_create'), 'step' => $request->next_step]),
            ]);
            break;
        case '3':
            return response()->json([
                'status'   => 'success',
                'message'  => __('Updated successfully'),
                'redirect' => route('instructor.courses.edit', ['id' => Session::get('course_create'), 'step' => $request->next_step]),
            ]);
        case '4':
            return response()->json([
                'status'   => 'success',
                'message'  => __('Updated successfully'),
                'redirect' => route('instructor.courses.edit', ['id' => Session::get('course_create'), 'step' => $request->next_step]),
            ]);
        case '5':
            $request->validate([
                'status'               => ['required'],
                'message_for_reviewer' => ['nullable', 'max:1000'],
            ]);
            $this->storeFinish($request);
            return response()->json([
                'status'   => 'success',
                'message'  => __('Course Updated Successfully'),
                'redirect' => $request->next_step == 5 ? route('instructor.courses.index') : route('instructor.courses.edit', ['id' => Session::get('course_create'), 'step' => $request->next_step]),
            ]);

        default:
            # code...
            break;
        }
    }

    public function storeMoreInfo(Request $request) {
        $course = Course::findOrFail($request->course_id);
        abort_if($course->instructor_id != userAuth()->id, 403);
        $course->capacity = $request->capacity;
        $course->duration = $request->course_duration;
        $course->category_id = $request->category;
        $course->qna = $request->qna;
        $course->downloadable = $request->downloadable;
        $course->certificate = $request->certificate;
        $course->partner_instructor = $request->partner_instructor;
        $course->save();

        // delete unselected partner instructor
        CoursePartnerInstructor::where('course_id', $course->id)
            ->whereNotIn('instructor_id', $request->partner_instructors ?? [])->delete();

        // insert partner instructor
        foreach ($request->partner_instructors ?? [] as $instructor) {
            CoursePartnerInstructor::updateOrCreate(
                ['course_id' => $course->id, 'instructor_id' => $instructor],
            );
        }

        // insert levels
        CourseSelectedLevel::where('course_id', $course->id)
            ->whereNotIn('level_id', $request->levels ?? [])->delete();

        foreach ($request->levels ?? [] as $level) {
            CourseSelectedLevel::updateOrCreate(
                ['course_id' => $course->id, 'level_id' => $level],
            );
        }

        //insert languages
        CourseSelectedLanguage::where('course_id', $course->id)
            ->whereNotIn('language_id', $request->languages ?? [])->delete();

        foreach ($request->languages ?? [] as $language) {
            CourseSelectedLanguage::updateOrCreate(
                ['course_id' => $course->id, 'language_id' => $language],
            );
        }
    }

    public function storeFinish(Request $request) {
        $course = Course::findOrFail($request->course_id);
        abort_if($course->instructor_id != userAuth()->id, 403, __('unauthorized access'));
        $course->message_for_reviewer = $request->message_for_reviewer;
        $course->status = $request->status;
        $course->save();
    }

    public function getInstructors(Request $request) {
        $instructors = User::where('role', 'instructor')
            ->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('email', 'like', '%' . $request->q . '%');
            })
            ->where('id', '!=', auth()->id())
            ->get();
        return response()->json($instructors);
    }

    public function getFiltersByCategory(string $id) {
        $levels = CourseLevel::with(['translation'])->where('status', 1)->get();
        $category = CourseCategory::find($id);
        $languages = CourseLanguage::where('status', 1)->get();
        return view('frontend.instructor-dashboard.course.partials.filters', compact('levels', 'category', 'languages'))->render();
    }

    public function showDeleteRequest(Request $request, $id) {
        return view('frontend.instructor-dashboard.course.partials.course-delete-request-modal', compact('id'));
    }

    public function sendDeleteRequest(Request $request) {

        $request->validate([
            'message' => ['required', 'max:1000'],
        ], ['message.required' => __('message is required'), 'message.max' => __('message should not be more than 1000 characters')]);
        $course = Course::findOrFail($request->course_id);
        if ($course->instructor_id != userAuth()->id) {
            abort(403);
        }
        // check if there is already a request
        if (CourseDeleteRequest::where('course_id', $course->id)->exists()) {
            return redirect()->back()->with(['messege' => __('you already have a pending request for this course'), 'alert-type' => 'error']);
        }

        $deleteRequest = new CourseDeleteRequest();
        $deleteRequest->course_id = $course->id;
        $deleteRequest->message = $request->message;
        $deleteRequest->save();

        return redirect()->back()->with(['messege' => __('Request sent successfully'), 'alert-type' => 'success']);
    }
}
