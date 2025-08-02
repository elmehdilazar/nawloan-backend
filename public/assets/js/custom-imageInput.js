$(document).ready(function () {
    $('#imageUpload, .imageUpload').addClass('dragging').removeClass('dragging');

    $('#imageUpload, .imageUpload').on('dragover', function () {
        $('#imageUpload, .imageUpload').addClass('dragging')
    }).on('dragleave', function () {
        $('#imageUpload, .imageUpload').removeClass('dragging')
    }).on('drop', function (e) {
        $('#imageUpload, .imageUpload').removeClass('dragging hasImage');

        if (e.originalEvent) {
            var file = e.originalEvent.dataTransfer.files[0];
            // console.log(file);

            var reader = new FileReader();

            //attach event handlers here...

            reader.readAsDataURL(file);
            reader.onload = function (e) {
                // console.log(reader.result);
                $('#imageUpload, .imageUpload').css('background-image', 'url(' + reader.result + ')')
                    .css('background-color', '#FFF')
                    .addClass('hasImage');
            }
            console.log($('.mediaFile').val())
            // $('.imageUpload-wrapper').removeClass('input-empty');
        }
    })

    window.addEventListener("dragover", function (e) {
        e = e || event;
        e.preventDefault();
    }, false);
    window.addEventListener("drop", function (e) {
        e = e || event;
        e.preventDefault();
    }, false);


    $('.mediaFile').each(function () {
        $(this).change(function (e) {
            var input = e.target;
            if (input.files && input.files[0]) {
                var file = input.files[0];
                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function (e) {
                    var $imageUpload = $(input).closest('.imageUpload-wrapper').find('#imageUpload');
                    $imageUpload.css('background-image', 'url(' + reader.result + ')')
                        .css('background-color', '#FFF')
                        .addClass('hasImage');
                }
            }
            console.log($('.mediaFile').val())
        });
    });

    $('#clear-input, .clear-input').on('click', function (e) {
        e.preventDefault();
        $(this).siblings('#mediaFile, .mediaFile').val('');
        $(this).parent('#imageUpload, .imageUpload')
            .css('background-image', 'none')
            .css('background-color', 'transparent')
            .removeClass('hasImage');
        // $('.imageUpload-wrapper').addClass('input-empty');
    });
})
