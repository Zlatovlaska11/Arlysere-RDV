@include('errors.layout', [
    'code'    => 503,
    'title'   => 'Service indisponible',
    'message' => 'L\'application est en maintenance. Elle sera de retour très bientôt.',
])
