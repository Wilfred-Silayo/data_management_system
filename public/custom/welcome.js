$(document).ready(function () {
    //show and hide search field upon clicking search icon
    $("#search-icon").click(function () {
        const searchField = $("#search-field");

        // Toggle visibility classes
        if (searchField.hasClass("d-none")) {
            searchField.removeClass("d-none").addClass("d-block");
        } else {
            searchField.addClass("d-none").removeClass("d-block");
        }
    });

    //upload file
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

    //export xlsx file
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

    // global variable per page
    var perPage = 15;

    // load data to the table
    function loadTableData(
        page = 1,
        perPage,
        search = "",
        sortColumn = "id",
        sortOrder = "asc"
    ) {
        $.ajax({
            url: "/fetch-data",
            type: "GET",
            data: {
                page: page,
                perPage: perPage,
                search: search,
                sortColumn: sortColumn,
                sortOrder: sortOrder,
            },
            success: function (response) {
                let tbody = $("#dataTable tbody");
                tbody.empty();

                if (response.data.data.length > 0) {
                    // console.log(response.data.data);
                    $.each(response.data.data, function (index, item) {
                        const rowIndex =
                            index + 1 + (page - 1) * parseInt(perPage);
                        tbody.append(`
                            <tr>
                                <td>${rowIndex}</td>
                                <td>${item.cut}</td>
                                <td>${item.color}</td>
                                <td>${item.clarity}</td>
                                <td>${item.carat_weight}</td>
                                <td>${item.cut_quality}</td>
                                <td>${item.lab}</td>
                                <td>${item.symmetry}</td>
                                <td>${item.polish}</td>
                                <td>${item.eye_clean}</td>
                                <td>${item.culet_size}</td>
                                <td>${item.culet_condition}</td>
                                <td>${item.depth_percent}</td>
                                <td>${item.table_percent}</td>
                                <td>${item.meas_length}</td>
                                <td>${item.meas_width}</td>
                                <td>${item.meas_depth}</td>
                                <td>${item.girdle_min}</td>
                                <td>${item.girdle_max}</td>
                                <td>${item.fluor_color}</td>
                                <td>${item.fluor_intensity}</td>
                                <td>${item.fancy_color_dominant_color}</td>
                                <td>${item.fancy_color_secondary_color}</td>
                                <td>${item.fancy_color_overtone}</td>
                                <td>${item.fancy_color_intensity}</td>
                                <td>${item.total_sales_price}</td>
                            </tr>
                        `);
                    });
                    // console.log(response.pagination);
                    $("#no-data").html("");
                    $("#pagination-links").html(response.pagination);
                } else {
                    $("#no-data").html(
                        '<div class="alert alert-danger" role="alert">No data available. Please reload.</div>'
                    );
                }
            },
            error: function (xhr) {
                console.log(xhr);

                $("#no-data").html(
                    '<div class="alert alert-danger" role="alert">An error occurred while fetching data. Please reload.</div>'
                );
            },
        });
    }

    // Initial table load
    loadTableData(1, perPage);

    //pagination links click
    $(document).on("click", ".pagination a", function (e) {
        e.preventDefault();

        let page = $(this).attr("href").split("page=")[1];

        loadTableData(page, perPage);
    });

    // searching by keywords
    $("#search-field").on("keyup", function () {
        const search = $(this).val();
        loadTableData(1, perPage, search);
    });

    //filter by per page
    $(".dropdown-menu a").on("click", function (e) {
        e.preventDefault();

        perPage = $(this).data("perpage");

        loadTableData(1, perPage);
    });

    // sorting by columns
    $.ajax({
        url: "/fetch-columns",
        type: "GET",
        success: function (columns) {
            // Populate the dropdown with column names
            let sortDropdown = $("#sort-dropdown");

            // Dynamically populate the dropdown with column names
            columns.forEach(function (column) {
                sortDropdown.append(`
                    <li class="dropdown-item dropdown-toggle">
                        ${column}
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item sort-option" href="#" data-column="${column}" data-order="asc">Asc</a></li>
                            <li><a class="dropdown-item sort-option" href="#" data-column="${column}" data-order="desc">Desc</a></li>
                        </ul>
                    </li>
                `);
            });

             // Prevent submenu from collapsing
             $(".dropdown-item.dropdown-toggle").on("click", function (e) {
                const $submenu = $(this).children(".dropdown-menu");
                $submenu.toggle(); 
                e.stopPropagation(); 
            });

            // Add click event to sort options
            $(".sort-option").on("click", function (e) {
                e.preventDefault();

                const column = $(this).data("column");
                const order = $(this).data("order");
                let search = "";

                loadTableData(1, perPage, search, column, order);
            });
        },
    });
});
