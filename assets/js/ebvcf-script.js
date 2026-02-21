(function($){
  // Espera até o Elementor frontend estar carregado
  $(window).on('elementor/frontend/init', function(){

    if(typeof ebvcf_rules === 'undefined' || !Array.isArray(ebvcf_rules) || !ebvcf_rules.length) return;

    // Filtra regras pelo escopo
    var applicableRules = ebvcf_rules.filter(function(r){
      if(r.scope === 'site') return true;
      if(r.scope === 'page') return BGVSettings.currentPageId === r.page_id;
      return false;
    });

    applicableRules.forEach(function(rule){
      if(!rule.selector || !rule.video_id) return;
      var sel = rule.selector.trim().startsWith('#') || rule.selector.trim().startsWith('.') ? rule.selector.trim() : '#'+rule.selector.trim();

      var checkAndInject = function(){
        var $els = $(sel);
        if(!$els.length) {
          // tenta novamente após 500ms caso Elementor ainda não tenha renderizado a seção
          setTimeout(checkAndInject, 500);
          return;
        }

        $els.each(function(){
          var $el = $(this);
          if($el.data('ebvcf-inited-'+rule.video_id)) return;
          $el.data('ebvcf-inited-'+rule.video_id,true).css({position: function(_,v){return (!v||v==='static')?'relative':v;},overflow:'hidden'});

          var $cont = $('<div class="ebvcf-bg-video-container"></div>').css({position:'absolute',top:0,left:0,width:'100%',height:'100%',overflow:'hidden',zIndex:-1,pointerEvents:'none'});

          if(rule.fallback_image_url){
            $('<img class="ebvcf-fallback-image">').attr('src',rule.fallback_image_url).css({position:'absolute',top:'50%',left:'50%',transform:'translate(-50%,-50%)',minWidth:'100%',minHeight:'100%',objectFit:'cover',zIndex:0,transition:'opacity .6s ease',opacity:1}).appendTo($cont);
          }

          $('<div class="ebvcf-overlay"></div>').css({position:'absolute',top:0,left:0,width:'100%',height:'100%',backgroundColor:rule.overlay_color||'#000',opacity:rule.overlay_opacity!=null?rule.overlay_opacity:0.4,zIndex:1,pointerEvents:'none',transition:'opacity .6s ease'}).appendTo($cont);

          $el.prepend($cont);

          setTimeout(function(){
            var $wrap = $('<div class="ebvcf-video-wrapper"></div>').css({position:'absolute',top:0,left:0,width:'100%',height:'100%',overflow:'hidden',zIndex:0});
            var src = (rule.privacy?'https://www.youtube-nocookie.com/embed/':'https://www.youtube.com/embed/') + rule.video_id + '?autoplay=1&mute=1&controls=0&loop=1&playlist='+rule.video_id+'&rel=0&modestbranding=1&playsinline=1';
            var $iframe = $('<iframe>',{src:src,frameborder:0,allow:'autoplay;encrypted-media',allowfullscreen:true,loading:'lazy'}).css({position:'absolute',top:'50%',left:'50%',transform:'translate(-50%,-50%) scale(1)',pointerEvents:'none',visibility:'hidden'});

            $wrap.append($iframe).appendTo($cont);

            function adjustCover(){
              var cw=$wrap.width(),ch=$wrap.height(),ar=16/9;
              var sw=cw/(ch*ar),sh=ch/(cw/ar),sc=Math.max(sw,sh)*1.05;
              if(cw/ch>ar) $iframe.css({width:ch*ar+'px',height:ch+'px'});
              else $iframe.css({width:cw+'px',height:cw/ar+'px'});
              $iframe.css('transform','translate(-50%,-50%) scale('+sc+')');
            }

            $iframe.on('load',function(){adjustCover();$iframe.css('visibility','visible');$cont.find('.ebvcf-fallback-image').css('opacity',0).delay(600).queue(function(){$(this).remove();});});
            $(window).on('resize.ebvcf',adjustCover);
            setTimeout(adjustCover,100);
          },(rule.delay||0)*1000);

        });
      };
      checkAndInject();
    });

  });
})(jQuery);