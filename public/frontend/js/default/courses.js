"use strict";

/** Variables */
const supported_source = [
    "upload",
    "youtube",
    "vimeo",
    "external_link",
    "google_drive",
    "iframe",
    "aws",
];

/** Functions */

/** On Document Load */
$(document).ready(function () {
    if ($("#demo_video_storage").val() == "upload") {
        $(".upload").removeClass("d-none");
        $(".external_link").addClass("d-none");
    }
    $("#demo_video_storage").on("change", function () {
        let select_source = $(this).val();
        if (select_source == "upload") {
            $(".upload").removeClass("d-none");
            $(".external_link").addClass("d-none");
        } else {
            $(".upload").addClass("d-none");
            $(".external_link").removeClass("d-none");
        }
    });

    // course Create form
    $(".course-form").on("submit", function (e) {
        e.preventDefault();
        let url = $(this).attr("action");
        let formData = new FormData(this);
        $.ajax({
            method: "POST",
            url: url,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $(".preloader").removeClass("d-none");
            },
            success: function (response) {
                if (response.status == "success") {
                    toastr.success(response.message);
                    window.location.href = response.redirect;
                }
            },
            error: function (xhr, status, error) {
                $(".preloader").addClass("d-none");

                let errors = xhr.responseJSON.errors;
                Object.entries(errors).forEach(([key, value]) => {
                    toastr.error(value);
                });
            },
        });
    });

    // show/hide partner instructor div
    $(".partner_instructor_btn").on("change", function () {
        if ($(this).is(":checked")) {
            $(".partner_instructor_list").removeClass("d-none");
        } else {
            $(".partner_instructor_list").addClass("d-none");
        }
    });

    // get instructor profiles for select2
    $(".partner_instructor_select").select2({
        ajax: {
            url: "/instructor/courses/get-instructors",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data,
                    pagination: {
                        more: false,
                    },
                };
            },
            cache: true,
        },
        placeholder: search_instructor_placeholder,
        minimumInputLength: 3,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection,
    });

    function formatRepo(repo) {
        if (repo.loading) {
            return repo.text;
        }

        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__avatar'><img src='" +
                "/" +
                repo.image +
                "' /></div>" +
                "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__title'>" +
                repo.name +
                "</div>" +
                "<div class='select2-result-repository__description'>" +
                repo.email +
                "</div>" +
                "</div>" +
                "</div>"
        );

        return $container;
    }

    function formatRepoSelection(repo) {
        return repo.name || repo.text;
    }

    /** make accordion child's sortable */
    if ($(".course-section .accordion-body").length) {
        $(".course-section .accordion-body").sortable({
            items: "> .card",
            containment: "parent",
            cursor: "move",
            handle: ".dragger",
            tolerance: "pointer",
            // function on update
            update: function (event, ui) {
                let orderIds = $(this).sortable("toArray", {
                    attribute: "data-chapter-item-id",
                });
                let chapterId = ui.item.data("chapterid");
                let csrf_token = $('meta[name="csrf-token"]').attr("content");
                $.ajax({
                    method: "post",
                    url:
                        base_url +
                        "/instructor/course-chapter/lesson/sorting/" +
                        chapterId,
                    data: {
                        _token: csrf_token,
                        orderIds: orderIds,
                    },
                    success: function (data) {
                        if (data.status == "success") {
                            toastr.success(data.message);
                        }
                    },
                });
            },
        });
    }

    /** handle chapter sorting modal */
    $(".sort-chapter-btn").on("click", function () {
        $(".dynamic-modal").modal("show");
        let courseId = $('meta[name="course_id"]').attr("content");
        $.ajax({
            method: "get",
            url: base_url + "/instructor/course-chapter/sorting/" + courseId,
            beforeSend: function () {
                dynamicModalContent.html(loader);
            },
            success: function (data) {
                dynamicModalContent.html(data);

                $(".draggable-list").sortable({
                    containment: "parent",
                    cursor: "move",
                    handle: ".dragger",
                    tolerance: "pointer",
                });
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
        });
    });
    // show add course section modal
    $(".add-course-section-btn").on("click", function () {
        $(".add-course-section-modal").modal("show");
    });

    $("body").on("change", ".add_lesson_form #source, .update_lesson_form #source", function () {
        const select_source = $(this).val();
        const isUpload = select_source === "upload";
        const isCloud = (select_source == "wasabi" || select_source == "aws");
        const isMediaSource = ['youtube', 'vimeo'].includes(select_source);
    
        // Reset file type options visibility
        $("#file_type option").show();
        
        // Manage visibility based on source type
        $(".upload").toggleClass("d-none", !isUpload);
        $(".cloud_storage").toggleClass("d-none", !isCloud);
        $(".link_path").toggleClass("d-none", isUpload);
        $("#input_link").attr("readonly", isCloud);

        // Toggle file type options
        $("#file_type option").each(function () {
            const showOption = isMediaSource ? $(this).val() === "video" : true;
            $(this).toggle(showOption);
        });

        // Reset the file type selection
        $("#file_type").val("");
    });

    $("body").on("change", "#file_type", function () {
        let filetype = $(this).val();
        if (filetype != "video") {
            $("#duration").val(0);
            $("#duration").attr("readonly", true);
            $(".is_free_wrapper").addClass("d-none");
        } else {
            $("#duration").val("");
            $("#duration").attr("readonly", false);
            $(".is_free_wrapper").removeClass("d-none");
        }
    });

    $("body").on("change", "#live_type", function () {
        let live_type = $(this).val();
        if (live_type === 'jitsi') {
            $(".meeting-id-box").removeClass('col-md-6').addClass('col-md-12');
            $(".zoom-box").addClass('d-none');
            $(".zoom-alert-box").addClass('d-none');
            $(".jitsi-alert-box").removeClass('d-none');
        } else {
            $(".meeting-id-box").removeClass('col-md-12').addClass('col-md-6');
            $(".zoom-box").removeClass('d-none');
            $(".zoom-alert-box").removeClass('d-none');
            $(".jitsi-alert-box").addClass('d-none');
        }
    });

    /** load edit chapter modal */
    $(".edit-chapter-btn").on("click", function () {
        $(".dynamic-modal").modal("show");
        let chapterId = $(this).data("chapterid");
        $.ajax({
            method: "GET",
            url: base_url + "/instructor/course-chapter/edit/" + chapterId,
            beforeSend: function () {
                dynamicModalContent.html(loader);
            },
            success: function (data) {
                dynamicModalContent.html(data);
            },
            error: function (xhr, status, error) {
                toastr(error);
            },
        });
    });

    /** load add new lesson modal */
    $(".add-lesson-btn").on("click", function () {
        $(".dynamic-modal").modal("show");
        let type = $(this).data("type");
        let chapterId = $(this).data("chapterid");
        let courseId = $('meta[name="course_id"]').attr("content");
        $.ajax({
            method: "GET",
            url: base_url + "/instructor/course-chapter/lesson/create",
            data: {
                type: type,
                chapterId: chapterId,
                courseId: courseId,
            },
            beforeSend: function () {
                dynamicModalContent.html(loader);
            },
            success: function (data) {
                dynamicModalContent.html(data);
                $(".file-manager").filemanager("file", {
                    prefix: base_url + "/frontend-filemanager",
                });
            },
            error: function (xhr, status, error) {
                toastr(error);
            },
        });
    });

    /** upload cloud storage */
    $("body").on("click", "#cloud-btn", function (e) {
        e.preventDefault();
        $(".progress").removeClass("d-none");
        $("body .dynamic-modal .submit-btn").prop("disabled", true);
        var fileInput = $('#file-input')[0];

        // Check if files are selected
        if (fileInput.files.length === 0) {
            toastr.error('Please select a file.');
            $(".progress").addClass("d-none");
            $("body .dynamic-modal .submit-btn").prop("disabled", false);
            return;
        }
        var formData = new FormData();
        formData.append('_token',$('meta[name="csrf-token"]').attr("content"));
        formData.append('file', fileInput.files[0]);
        formData.append('source', $("#source").val());

        $.ajax({
            url: base_url + '/instructor/cloud/store',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if(response.status == "success"){
                    $("#input_link").val(response.path);
                    toastr.success(response.message);
                }else{
                    toastr.error(response.message);
                }
                fileInput.value = '';
            },
            error: function (xhr, status, error) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    toastr.error(value);
                });
            },
            complete: function(){
                $(".progress").addClass("d-none");
                $("body .dynamic-modal .submit-btn").prop("disabled", false);
            }
        });
    });

    /** create new lesson */
    $("body").on("submit", ".add_lesson_form", function (e) {
        e.preventDefault();
        let url = $(this).attr("action");
        $.ajax({
            method: "POST",
            url: url,
            data: $(this).serialize(),
            beforeSend: function () {
                $("body .dynamic-modal .submit-btn")
                    .prop("disabled", true)
                    .text("loading...");
            },
            success: function (data) {
                $(".dynamic-modal").modal("hide");
                toastr.success(data.status);
                window.location.reload();
            },
            error: function (xhr, status, error) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    toastr.error(value);
                });
                $("body .dynamic-modal .submit-btn")
                    .prop("disabled", false)
                    .text("Create");
            },
        });
    });

    /** load lesson update modal */
    $(".edit-lesson-btn").on("click", function () {
        $(".dynamic-modal").modal("show");
        let type = $(this).data("type");
        let chapterId = $(this).data("chapterid");
        let chapterItemId = $(this).data("chapter_item_id");
        let courseId = $('meta[name="course_id"]').attr("content");
        $.ajax({
            method: "GET",
            url: base_url + "/instructor/course-chapter/lesson/edit",
            data: {
                type: type,
                chapterId: chapterId,
                courseId: courseId,
                chapterItemId: chapterItemId,
            },
            beforeSend: function () {
                dynamicModalContent.html(loader);
            },
            success: function (data) {
                $(".dynamic-modal .modal-content").html(data);
                $(".file-manager").filemanager("file", {
                    prefix: base_url + "/frontend-filemanager",
                });
                $('input[data-toggle="toggle"]').bootstrapToggle();
            },
            error: function (xhr, status, error) {
                toastr(error);
            },
        });
    });

    // update lesson
    $("body").on("submit", ".update_lesson_form", function (e) {
        e.preventDefault();
        let url = $(this).attr("action");
        $.ajax({
            method: "POST",
            url: url,
            data: $(this).serialize(),
            beforeSend: function () {
                $("body .dynamic-modal .submit-btn")
                    .prop("disabled", true)
                    .text("loading...");
            },
            success: function (data) {
                $(".dynamic-modal").modal("hide");
                toastr.success(data.message);
                window.location.reload();
            },
            error: function (xhr, status, error) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    toastr.error(value);
                });
                $("body .dynamic-modal .submit-btn")
                    .prop("disabled", false)
                    .text("Create");
            },
        });
    });

    /** load add new quiz modal */
    $(".add-quiz-question-btn").on("click", function () {
        $(".dynamic-modal").modal("show");
        let quizId = $(this).data("quiz-id");
        $.ajax({
            method: "GET",
            url:
                base_url +
                "/instructor/course-chapter/quiz-question/create/" +
                quizId,
            data: {},
            beforeSend: function () {
                dynamicModalContent.html(loader);
            },
            success: function (data) {
                $(".dynamic-modal .modal-content").html(data);
            },
            error: function (xhr, status, error) {
                toastr(error);
            },
        });
    });

    // load edit question model
    $(".edit-question-btn").on("click", function () {
        $(".dynamic-modal").modal("show");
        let questionId = $(this).data("question-id");
        $.ajax({
            method: "GET",
            url:
                base_url +
                "/instructor/course-chapter/quiz-question/edit/" +
                questionId,
            data: {},
            beforeSend: function () {
                dynamicModalContent.html(loader);
            },
            success: function (data) {
                $(".dynamic-modal .modal-content").html(data);
            },
            error: function (xhr, status, error) {
                toastr(error);
            },
        });
    });

    // Add Answer button click event
    $("body").on("click", ".add-answer", function () {
        var randomId = Math.random().toString(36).substring(2, 11);
        var answerContainer = $(".answer-container");
        var newAnswerCard = $(".answer-container .card:first").clone(true);
        newAnswerCard.find(".answer").attr("name", "answers[" + randomId + "]");
        newAnswerCard
            .find(".correct")
            .attr("name", "correct[" + randomId + "]");

        newAnswerCard.find(".answer").val(""); // Clear input value
        newAnswerCard.find(".correct").prop("checked", false);
        newAnswerCard
            .find('input[type="checkbox"]')
            .attr("id", "answer-" + randomId);
        newAnswerCard.find("label").attr("for", "answer-" + randomId);
        answerContainer.append(newAnswerCard);
    });

    // Remove Answer button click event
    $("body").on("click", ".remove-answer", function () {
        if ($(this).closest(".card").is(":first-child")) {
            return false;
        }

        $(this).closest(".card").remove();
    });
});
