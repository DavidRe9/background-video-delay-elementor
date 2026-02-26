(function($){
  function normalize(raw){
    if(!raw) return '';
    raw=raw.trim();
    if(raw.startsWith('#')||raw.startsWith('.')) return raw;
    if(document.querySelector('#'+raw)) return '#'+raw;
    if(document.querySelector('.'+raw)) return '.'+raw;
    return raw;
  }
  function extractID(u){
    if(!u) return '';
    var m=u.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]{11})/);
    if(m) return m[1];
    return u.trim().slice(-11);
  }
  function onReady(cb,attempts){
    if(typeof ebvcf_rules!=='undefined') return cb();
    if(attempts>0) return setTimeout(()=>onReady(cb,attempts-1),200);
    // console.warn('EBVCF: ebvcf_rules indefinido');
  }
  $(function(){
    onReady(function(){
      ebvcf_rules.forEach(function(r){
        var sel=normalize(r.selector), $el=$(sel);
        if(!$el.length) return;
        $el.each(function(){
          var $cont=$(this);
          if($cont.data('inited-'+r.video_id)) return;
          $cont.data('inited-'+r.video_id,true).css('position',function(i,v){return(!v||v==='static')?'relative':v;});
          var $container=$('<div class="ebvcf-bg-video-container"></div>').css({position:'absolute',inset:0,overflow:'hidden',zIndex:0});
          if(r.fallback_image_url){
            $container.append($('<img class="ebvcf-fallback-image">').attr('src',r.fallback_image_url).css({position:'absolute',top:'50%',left:'50%',transform:'translate(-50%,-50%)','min-width':'100%','min-height':'100%',objectFit:'cover',transition:'opacity .6s',opacity:1}));
          }
          
          try{$container.find('img.ebvcf-fallback-image').attr('alt', (r.fallback_image_alt||'Background image'));}catch(e){}
          $container.append($('<div class="ebvcf-overlay"></div>').css({position:'absolute',inset:0,backgroundColor:r.overlay_color,opacity:r.overlay_opacity,zIndex:2,pointerEvents:'none'}));
          $cont.prepend($container);
          setTimeout(function(){
            var $wrap=$('<div class="ebvcf-video-wrapper"></div>').css({position:'absolute',inset:0,overflow:'hidden',zIndex:1});
            var id=extractID(r.video_id);
            var src=(r.privacy?'https://www.youtube-nocookie.com/embed/':'https://www.youtube.com/embed/')+id+'?autoplay=1&mute=1&loop=1&controls=0&playlist='+id+'&rel=0&modestbranding=1&fs=0&disablekb=1&playsinline=1&iv_load_policy=3&showinfo=0&origin='+encodeURIComponent(window.location.origin);
            var $iframe=$('<iframe>').attr({src:src,frameborder:0,allow:'autoplay;encrypted-media',allowfullscreen:true,loading:'lazy',title:'Background Video'}).css({position:'absolute',top:'50%',left:'50%',transform:'translate(-50%,-50%) scale(1)',visibility:'hidden'});
            $wrap.append($iframe); $container.append($wrap);
            function adjust(){
              var w=$wrap.width(),h=$wrap.height(),ar=16/9;
              var sw=w/(h*ar),sh=h/(w/ar),sc=Math.max(sw,sh)*1.05;
              if(w/h>ar) $iframe.css({height:h,width:ar*h});
              else $iframe.css({width:w,height:w/ar});
              $iframe.css('transform','translate(-50%,-50%) scale('+sc+')');
            }
            $iframe.on('load',function(){adjust();$iframe.css('visibility','visible');$container.find('.ebvcf-fallback-image').css('opacity',0).delay(600).queue(function(){ $(this).remove(); });});
            $(window).on('resize',adjust);setTimeout(adjust,100);
          }, r.delay*1000);
        });
      });
    },15);
  });
})(jQuery);