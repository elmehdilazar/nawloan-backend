<!-- HTML for static distribution bundle build -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Nawloan Rest Api Docs</title>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/swagger-ui.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/index.css')}}" />
    <link rel="icon" type="image/png" href="{{asset('assets/img/favicon-32x32.png')}}" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{asset('assets/img/favicon-16x16.png')}}" sizes="16x16" />
  </head>

  <body>
    <div id="swagger-ui"></div>
    <script src="{{asset('assets/js/swagger-ui-bundle.js')}}" charset="UTF-8"> </script>
    <script src="{{asset('assets/js/swagger-ui-standalone-preset.js')}}" charset="UTF-8"> </script>
    {{-- <script src="{{asset('assets/js/swagger-initializer.js')}}" charset="UTF-8"> </script>
    --}} <script >
        window.onload = function() {
  //<editor-fold desc="Changeable Configuration Block">

  // the following lines will be replaced by docker/configurator, when it runs in a docker-container
  window.ui = SwaggerUIBundle({
    url: "{{asset('assets/Nawloan.json')}}",
    dom_id: '#swagger-ui',
    deepLinking: true,
    presets: [
      SwaggerUIBundle.presets.apis,
      SwaggerUIStandalonePreset
    ],
    plugins: [
      SwaggerUIBundle.plugins.DownloadUrl
    ],
    layout: "StandaloneLayout"
  });

  //</editor-fold>
};

</script>
  </body>
</html>
