var uptarg = document.querySelectorAll('.drag-drop-area');
if (uptarg) {
    uptarg.forEach(area => {
        var uppy = Uppy.Core().use(Uppy.Dashboard, {
            inline: true,
            target: area,
            proudlyDisplayPoweredByUppy: false,
            theme: 'dark',
            width: 770,
            height: 190,
            plugins: ['Webcam'],
            restrictions: {
                maxNumberOfFiles: 1,
                allowedFileTypes: ['.jpg', '.jpeg', '.png']
            }
        }).use(Uppy.Tus,{
            endpoint: 'https://master.tus.io/files/'
        });
        console.log(area.parentNode.querySelector('input[type="hidden"]'))

        uppy.on('upload-success', (file, response) => {
            // Get the uploaded file URL from the response
            const uploadedFileUrl = response.uploadURL;

            // Set the file URL to the hidden input element
            const hiddenInput = area.parentNode.querySelector('input[type="hidden"]');
            hiddenInput.value = uploadedFileUrl;
            console.log(hiddenInput.value)
        });

        uppy.on('file-added', () => {
            // Upload the new file after the file has been added or dropped inside the area
            uppy.upload();
        });

        uppy.on('error', (error) => {
            console.error('Error uploading files:', error);
        });
    });
}