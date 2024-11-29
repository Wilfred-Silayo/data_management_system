$(document).ready(function () {
    $("#search-icon").click(function () {
        const searchField = $("#search-field");

        // Toggle visibility classes
        if (searchField.hasClass("d-none")) {
            searchField.removeClass("d-none").addClass("d-block");
        } else {
            searchField.addClass("d-none").removeClass("d-block");
        }
    });

    $(".dropdown-item.dropdown-toggle").click(function (e) {
        var $submenu = $(this).children(".dropdown-menu");

        $submenu.toggle();

        e.stopPropagation();
    });

    $("#fileUploadForm").on("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        $("#uploadStatus").removeClass("text-danger");
        $("#upload-btn").addClass("disabled");
        $("#uploadStatus").text("Uploading...");
        $("#file-error-field").text("");

        $.ajax({
            url: "/upload-file",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log(response);
                if (response.success) {
                    // console.log(response.data);
                    $("#uploadStatus").text(
                        "File uploaded. Processing started..."
                    );
                    checkProcessingStatus(response.jobId); // Start polling
                    $("#upload-btn").removeClass("disabled");
                } else {
                    $("#upload-btn").removeClass("disabled");

                    $("#uploadStatus").text("Error uploading file.");
                }
            },
            error: function (xhr) {
                // Clear previous messages
                $("#uploadStatus").text("");
                $("#file-error-field").text("");
                $(".form-control").removeClass("is-invalid");

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    console.log(errors);
                    for (let field in errors) {
                        $(`[name=${field}]`).addClass("is-invalid");

                        $(`#file-error-field`).text(errors[field][0]);
                    }

                    $("#uploadStatus").addClass("text-danger");
                    $("#upload-btn").removeClass("disabled");
                    $("#uploadStatus").text(
                        "Validation failed. Fix errors and try again."
                    );
                } else {
                    $("#uploadStatus").addClass("text-danger");
                    // console.log(xhr);
                    $("#upload-btn").removeClass("disabled");
                    $("#uploadStatus").text("An unexpected error occurred.");
                }
            },
        });
    });

    function checkProcessingStatus(jobId) {
        let interval = setInterval(function () {
            $.ajax({
                url: `/job-status/${jobId}`,
                type: "GET",
                success: function (response) {
                    if (response.finished) {
                        $("#uploadStatus").text("Processing complete.");
                        clearInterval(interval);
                    } else {
                        $("#uploadStatus").text(
                            `Processing... ${response.progress || ""}%`
                        );
                    }
                },
                error: function () {
                    $("#uploadStatus").text("Error checking status.");
                    clearInterval(interval);
                },
            });
        }, 2000); // Poll every 2 seconds
    }

    $("#export-xls").click(function () {
        $("#download-status").html("");

        $(this).addClass("disabled");

        $.ajax({
            url: "/export/xls",
            type: "GET",
            success: function (response) {
                if (response.success) {
                    const batchId = response.batchId;
                    checkProgress(batchId);
                    $(this).removeClass("disabled");
                } else {
                    $(this).removeClass("disabled");
                    $("#download-status").html(
                        `<p class="text-danger">${response.message}</p>`
                    );
                }
            },
            error: function (xhr) {
                $(this).removeClass("disabled");
                $("#download-status").html(
                    `<p class="text-danger">Error: ${xhr.responseText}</p>`
                );
            },
        });
    });

    // Function to check download progress
    function checkProgress(batchId) {
        const interval = setInterval(function () {
            $.ajax({
                url: `job-status/${batchId}`,
                type: "GET",
                success: function (response) {
                    if (response.finished) {
                        clearInterval(interval);
                        $("#download-status").html(`
                            <p class="text-success">Export completed! <a href="/export/xls/download" class="btn btn-link">Download File</a></p>
                        `);
                    } else {
                        $("#download-status").html(`
                            <p class="text-info">Export progress: ${response.progress}%</p>
                        `);
                    }
                },
                error: function (xhr) {
                    clearInterval(interval);
                    $("#download-status").html(
                        `<p class="text-danger">Error: ${xhr.responseText}</p>`
                    );
                },
            });
        }, 2000);
    }
});
