$(document).on('click', '.playlists-new', function() {
  var name = prompt('Введите название плейлиста', 'Новый плейлист');
  if ( name && name.trim() ) {
    $.ajax({
      url: '/addplaylist',
      type: 'post',
      data: {
        name: name
      },
      success: function( resp ) {
        $('#playlists').replaceWith(resp);
      }
    });
  }
});

$(document).on('click', '.playlist-delete', function() {
  if ( confirm('Уверены что хотите удалить плейлист?') ) {
    var id = $(this).parents('.playlist').data('id');
    $.ajax({
      url: '/deleteplaylist',
      type: 'post',
      data: {
        id: id
      },
      success: function( resp ) {
        $('#playlists').replaceWith(resp);
      }
    });
  }
});

$(document).on('click', '.a-next', function() {
  var next = $(curtrack).next();
  if ( !next.length ) {
    next = $('.track:first');
  }
  next.find('.track-play').trigger('click');

  $('html, body').animate({scrollTop: next.offset().top - 100}, 'fast');
});

$(document).on('click', '.a-prev', function() {
  var prev = $(curtrack).prev();
  if ( !prev.length ) {
    prev = $('.track:last');
  }
  prev.find('.track-play').trigger('click');

  $('html, body').animate({scrollTop: prev.offset().top - 100}, 'fast');
});

$(document).on('click', '.track .track-pause', function() {
  $(curtrack).find('.track-play').show();
  $(curtrack).find('.track-pause').hide();

  $("#jplayer").jPlayer("pause");
});

$(document).on('click', '.jp-play', function() {
  if ( !curtrack ) {
    curtrack = $('.track:first');
    if ( curtrack.length ) {
      curtrack.find('.track-play').trigger('click');
      $('html, body').animate({scrollTop: curtrack.offset().top - 100}, 'fast');
    }
  }
});

$(document).on('click', '.track .track-play', function() {
  curtrack = $(this).parents('.track');
    
  $('.playing').removeClass('playing');
  $(curtrack).addClass('playing');

  $('.track-pause').hide();
  $('.track-play').show();

  $('.player-artist').html( $(curtrack).find('.track-artist').html() );
  $('.player-title').html( $(curtrack).find('.track-title').html() );

  var mid = $(curtrack).data('mid');
  $("#jplayer").jPlayer("setMedia", {
    mp3: "/mp3/"+mid+".mp3"
  }).jPlayer("play");
});

$(document).on('click', '.track-addtoplaylist', function() {
  var self = this;
  $.ajax({
    url: '/loadplaylists',
    success: function( data ) {
      $(self).after(data);
      $('.playlists-list').fadeIn('fast');
    }
  });
});

$(document).on('click', '.track-delfromplaylist', function() {
  if ( confirm('Удалить трек из плейлиста?') ) {
    var mid = $(this).parents('.track').data('mid');
    var playlistId = $(this).data('playlistid');

    $.ajax({
      url: '/deltrackfromplaylist',
      type: 'post',
      data: {
        mid: mid,
        playlistId: playlistId
      },
      success: function( resp ) {
        $('#playlists').replaceWith(resp);
        load(location.href);
      }
    });
  }
});

$(document).on('click', '.playlist-item', function() {
  var mid = $(this).parents('.track').data('mid');
  var playlistId = $(this).data('id');

  $.ajax({
    url: '/addtracktoplaylist',
    type: 'post',
    data: {
      mid: mid,
      playlistId: playlistId
    },
    success: function( resp ) {
      $('#playlists').replaceWith(resp);
    }
  });
});

$(document).on('submit', '.searchform', function() {
  var href = $(this).attr('action');
  var q = $(this).find('input[name=q]').val();
  load( href + '?q=' + q );

  return false;
});

$(document).on('click', function() {
  $('.playlists-list').fadeOut('fast', function() {
    $(this).remove();
  });
});

$(document).on('click', '.donate-head', function() {
  $('.donate').addClass('donate-click');
});

$(document).on('click', '.donate-click', function() {
  $(this).removeClass('donate-click');
});

var curtrack = null;

$(document).ready(function() {
  var atop = $('#rightblock').offset().top;
  $(window).scroll(function() {
    $(this).scrollTop() >= (atop - 55)
      ? $('#rightblock').addClass('playerfixed')
      : $('#rightblock').removeClass('playerfixed');
  });

  $("#jplayer").jPlayer({
    solution: "flash, html",
    swfPath: "/lib/jQuery.jPlayer/",
    volume: "100",
    supplied: "mp3",
    keyEnabled: true
  });

  $("#jplayer").bind($.jPlayer.event.play, function(event) {
    $(curtrack).find('.track-play').hide();
    $(curtrack).find('.track-pause').show();
  });

  $("#jplayer").bind($.jPlayer.event.pause, function(event) {
    $(curtrack).find('.track-play').show();
    $(curtrack).find('.track-pause').hide();
  });

  $("#jplayer").bind($.jPlayer.event.ended, function(event) {
    $('.a-next').trigger('click');
  });
});

var timestamps = [];
$(document).ready(function() {
  History.Adapter.bind(window, 'statechange', function() {
    var State = History.getState();

    if ( State.data.timestamp in timestamps ) {        
      delete timestamps[State.data.timestamp];
    } else {
      load(State.url);
    }
  });
});

function load(href) {
  var t = new Date().getTime(); 
  timestamps[t] = t;
  History.pushState({timestamp:t}, null, href);

  $.ajax({
    url: href,
    data: {
      ajax: 1
    },
    success: function( resp ) {
      $('.subcontent').html($(resp).find('.subcontent').html());
    }
  });
}

$(document).on('click', 'a.noreload', function() {
  var href = $(this).attr('href');
  load(href);

  return false;
});

$(document).ajaxStart(function() {
  $('#loading').fadeIn('fast');
});

$(document).ajaxStop(function() {
  $('#loading').fadeOut('fast');
});

$(document).ajaxError(function() {
  $('#loading').fadeOut('fast');
  alert('Ошибка загрузки');
});
