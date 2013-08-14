<?php
namespace Controller;
use \Silex\Application,
  Project\Cache;

class Root implements \Silex\ControllerProviderInterface {
  public function connect( Application $app) {
    $index = $app['controllers_factory'];

    // $app->error(function (\Exception $e, $code) {
    //     switch ($code) {
    //         case 404:
    //             $message = 'The requested page could not be found.';
    //             break;
    //         default:
    //             $message = 'We are sorry, but something went terribly wrong.';
    //     }

    //     return new Response($message);
    // });
    
    $index->get('/', function( Application $app ) {
      $artists = Cache::get('geo.getTopArtists', 60*60*24*7, function() use ($app) {
        return $app['lastfm']->request('geo.getTopArtists', array(
          'country' => 'russia',
          'limit' => 100
        ));
      });

      return $app['twig']->render('root/index.twig', array(
        'artists' => $artists
      ));
    })->bind('index');

    $index->get('/playlists', function( Application $app ) {
      $playlists = \Model\PlaylistQuery::create()
        ->filterByUser($app['user']::get())
        ->orderByPosition()
        ->orderById('DESC')
        ->find();

      return $app['twig']->render('root/playlists.twig', array(
        'playlists' => $playlists
      ));
    })->bind('playlists');

    $index->get('/donate', function( Application $app ) {
      return $app['twig']->render('root/donate.twig', array(
      ));
    })->bind('donate');

    $index->get('/loadplaylists', function( Application $app ) {
      $playlists = \Model\PlaylistQuery::create()
        ->filterByUser($app['user']::get())
        ->orderByPosition()
        ->orderById('DESC')
        ->find();

      return $app['twig']->render('root/loadplaylists.twig', array(
        'playlists' => $playlists
      ));
    })->bind('loadplaylists');
    
    $index->post('/addplaylist', function( Application $app ) {
      if ( $app['user']::get() ) {
        $name = $app['request']->get('name');

        $playlist = new \Model\Playlist;
        $playlist->setUser($app['user']::get());
        $playlist->setName($name);
        $playlist->save();

        return $app->redirect(
          $app['url_generator']->generate('playlists')
        );
      }

      return '';
    })->bind('addplaylist');

    $index->get('/vkerror', function( Application $app ) {
      if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
          return "<div><div class='subcontent'><script>location.href = '/vkerror';</script></div></div>";
      }

      return $app['twig']->render('root/vkerror.twig', array(
      ));
    })->bind('vkerror');

    $index->post('/addtracktoplaylist', function( Application $app ) {
      if ( $app['user']::get() ) {
        $vkid = $app['request']->get('vkid');
        $playlistId = $app['request']->get('playlistId');

        $playlist = \Model\PlaylistQuery::create()
          ->filterByUser( $app['user']::get() )
          ->filterById( $playlistId )
          ->findOne();

        if ( $playlist ) {
          $vkTrack = Cache::get("vk_track_{$vkid}", 60*60*24*14, function() use ( $app, $vkid ) {
            return $app['openplayer']->audioGetById( $vkid );
          });

          $playlist->setCnt($playlist->getCnt() + 1);
          $playlist->save();

          $pt = new \Model\PlaylistTrack;
          $pt->setPlaylist( $playlist );
          $pt->setTrack( serialize($vkTrack) );
          $pt->save();
        }

        return $app->redirect(
          $app['url_generator']->generate('playlists')
        );
      }

      return '';
    })->bind('addtracktoplaylist');

    $index->post('/deltrackfromplaylist', function( Application $app ) {
      if ( $app['user']::get() ) {
        $vkid = $app['request']->get('vkid');
        $playlistId = $app['request']->get('playlistId');

        $playlist = \Model\PlaylistQuery::create()
          ->filterByUser( $app['user']::get() )
          ->filterById( $playlistId )
          ->findOne();

        if ( $playlist ) {
          $playlist->setCnt($playlist->getCnt() - 1);
          $playlist->save();

          $ptracks = \Model\PlaylistTrackQuery::create()
            ->filterByPlaylist($playlist)
            ->find();

          foreach ( $ptracks as $track ) {
            $inf = unserialize($track->getTrack());
            if ( $vkid == "{$inf->owner_id}_{$inf->aid}" ) {
              $track->delete();
            }
          }
        }

        return $app->redirect(
          $app['url_generator']->generate('playlists')
        );
      }

      return '';
    })->bind('deltrackfromplaylist');

    $index->post('/deleteplaylist', function( Application $app ) {
      if ( $app['user']::get() ) {
        $id = $app['request']->get('id');

        $playlist = \Model\PlaylistQuery::create()
          ->filterByUser($app['user']::get())
          ->filterById($id)
          ->findOne();
        $playlist->delete();

        return $app->redirect(
          $app['url_generator']->generate('playlists')
        );
      }

      return '';
    })->bind('deleteplaylist');

    $index->post('/poschange', function( Application $app ) {
      if ( $app['user']::get() ) {
        $ids = $app['request']->get('ids');
        $ids = explode(',', $ids);

        $playlists = \Model\PlaylistQuery::create()
          ->findPKs($ids);

        foreach ( $playlists as $key => $playlist ) {
          if ( $app['user']::get('id') == $playlist->getUserId() ) {
            $playlist->setPosition(array_search($playlist->getId(), $ids));
            $playlist->save();
          }
        }

        return '';
      }
    })->bind('poschange');

    $index->get('/track/{vkId}', function( Application $app, $vkId ) {
      $vkTrack = Cache::get("vk_track_{$vkId}", 60*60*24*14, function() use ($app, $vkId) {
        return $app['openplayer']->audioGetById( $vkId );
      });

      $track = array(
        'vkId' => "{$vkTrack->owner_id}_{$vkTrack->aid}",
        'url' => $vkTrack->url,
        'duration' => gmdate("i:s", $vkTrack->duration),
        'artist' => $vkTrack->artist,
        'title' => $vkTrack->title,
      );

      $lyrics = null;
      if ( isset($vkTrack->lyrics_id) && $lyricsId = $vkTrack->lyrics_id ) {
        $lyrics = Cache::get("vk_tracklyrics_{$vkTrack->lyrics_id}", 60*60*24*14, function() use ($app, $lyricsId) {
          return $app['openplayer']->audioGetLyrics( $lyricsId );
        });
      }

      return $app['twig']->render('root/track.twig', array(
        'track' => $track,
        'i' => 0,
        'playlistId' => null,
        'lyrics' => $lyrics
      ));
    })->bind('track');

    $index->get('/captcha', function( Application $app ) {
      $img = $app['request']->get('img');
      $sid = $app['request']->get('sid');

      return $app['twig']->render('root/captcha.twig', array(
        'img' => $img,
        'sid' => $sid
      ));
    })->bind('captcha');

    $index->post('/entercaptcha', function( Application $app ) {
      $sid = $app['request']->get('sid');
      $key = $app['request']->get('key');

      $app['openplayer']->search(
        'Test', 0, 1,
        $sid, $key
      );

      return $app->redirect('/');
    })->bind('entercaptcha');

    $index->get('/test', function( Application $app ) {
      die;
      for ( $i=0; $i < 100; $i++ ) { 
        $q = uniqid();
        $search = $app['openplayer']->search($q);
        error_log( $i . ':' . $q);
      }
      // $q = 'test';
      // $search = $app['openplayer']->search($q);
      // print_r($search);
      die;
    })->bind('test');

    $index->get('/mp3/{vkid}.mp3', function( Application $app, $vkid ) {
      session_write_close();

      $vkTrack = Cache::get("vk_track_{$vkid}", 60*60*24, function() use ($app, $vkid) {
        return $app['openplayer']->audioGetById( $vkid );
      });

      // If cached url is expired, recache track.
      $headers = get_headers($vkTrack->url);
      if ( 'HTTP/1.1 200 OK' != $headers[0] ) {
        $vkTrack = Cache::get("vk_track_{$vkid}", 60*60*24, function() use ($app, $vkid) {
          return $app['openplayer']->audioGetById( $vkid );
        }, true);
      }

      header("Content-Length: {$vkTrack->size}");

      if ( $app['request']->get('dl') ) {
          header('Last-Modified:');
          header('ETag:');
          header('Content-Type: audio/mpeg');
          header('Accept-Ranges: bytes');

          header("Content-Disposition: attachment; filename=\"{$vkTrack->fname}\"");
          header('Content-Description: File Transfer');
          header('Content-Transfer-Encoding: binary');

          echo file_get_contents($vkTrack->url);
          die;
      }

      return $app->stream(function () use ($vkTrack) {
        readfile($vkTrack->url);
      }, 200, array('Content-Type' => 'audio/mpeg'));
    })->bind('mp3');


    $index->get('/player', function( Application $app ) {

      return $app['twig']->render('root/player.twig', array());
    })->bind('player');

    $search = function( Application $app ) {
      $artist = urldecode($app['request']->get('artist'));
      $q = urldecode($app['request']->get('q', $artist));
      $playlistId = $app['request']->get('id');

      if ( $playlistId ) {
        $playlist = \Model\PlaylistQuery::create()
          ->findOneById($playlistId);
        $pt = \Model\PlaylistTrackQuery::create()
          ->findByPlaylist($playlist);

        $tracks = array();
        foreach ( $pt as $t ) {
          $inf = unserialize($t->getTrack());

          $track = array(
            'vkId' => "{$inf->owner_id}_{$inf->aid}",
            'url' => $inf->url,
            'duration' => gmdate("i:s", $inf->duration),
            'artist' =>$inf->artist,
            'title' =>$inf->title,
          );
          $tracks[] = $track;
        }
        $count = count($pt);
        $q = $playlist->getName();
      } else {
        $search = Cache::get("vk_search_{$q}", 60*60*24*7, function() use ($app, $q) {
          $search = $app['openplayer']->search($q);
          if ( $search['tracks'] ) {
            return $search;
          }

          return null;
        });
        $tracks = $search['tracks'];
        $count = $search['count'];
      }

      $artistInfo = null;
      if ( $artist ) {
        $artistInfo = Cache::get("artist.getInfo_{$artist}", 60*60*24*7, function() use ($app, $artist) {
          return $app['lastfm']->request('artist.getInfo', array(
            'artist' => $artist,
            'lang' => 'ru'
          ));
        });
      }

      return $app['twig']->render('root/search.twig', array(
        'tracks' => $tracks,
        'count' => $count,
        'artistInfo' => $artistInfo,
        'q' => $q,
        'playlistId' => $playlistId
      ));
    };
    $index->get('/search', $search)->bind('search');
    $index->get('/search/{artist}', $search)->bind('search.artist');
    $index->get('/playlist/{id}', $search)->bind('playlist');

    

    return $index;
  }


}