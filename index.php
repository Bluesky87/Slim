<?php


require 'vendor/autoload.php';
include 'bootstrap.php';

use Chatter\Models\Message;
use Chatter\Middleware\Logging as ChatterLogging;
use Chatter\Middleware\Authentication as ChatterAuth;
use Slim\Http\UploadedFile;
use Chatter\Middleware\FileRemoveExif;
use Chatter\Middleware\FileMove;
use Chatter\Middleware\FileFilter;

$app = new \Slim\App();
$app->add(new ChatterAuth());
$app->add(new ChatterLogging());


$filter = new FileFilter();
$removeExif = new FileRemoveExif();
$move = new FileMove();

$app->get('/', function ($request, $response, $args) {
    return $response->withStatus(200);
});

$app->group('/v1', function() {
    $this->group('/messages', function() {
        $this->map(['GET'], '', function($request, $response, $args) {
            $message = new Message();
            $messages = $message->all();

            $payload = [];
            foreach ($messages as $msg) {
                $payload[$msg->id] = $msg->output();
            }

            return $response->withStatus(200)->withJson($payload);
        })->setName('get_messages') ;
    });
});

$app->post('/messages', function ($request, $response, $args) {
    $_message = $request->getParsedBodyParam('message', '');

    $imagepath = '';

    $message = new Message();

    $message->body = $_message;
    $message->user_id = 2;
    $message->image_url = $imagepath;
    $message->save();

    if ($message->id) {
        $payload = ['message_id' => $message->id,
            'message_uri' => '/messages/' . $message->id];
        return $response->withStatus(201)->withJson($payload);
    } else {
        return $response->withStatus(400);
    }
});//->add($filter)->add($removeExif)->add($move)


$app->delete('/messages/{message_id}', function ($request, $response, $args) {
    $message = Message::find($args['message_id']);

    if ($message) {
        $message->delete();
    } else {
        return $response->withStatus(400);
    }

    if ($message->exists) {
        return $response->withStatus(400);
    } else {
        return $response->withStatus(204);
    }
});


$c = $app->getContainer();
$c['notAllowedHandler'] = function ($c) {
    return function ($request, $response, $methods) use ($c) {
        return $response->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-type', 'text/html');
    };
};

$app->run();