jQuery(function($){
  var mediaFrame;

  function updatePreview($row, attachment) {
    $row.find('.fallback-image-id').val(attachment.id);
    $row.find('.fallback-image-url').val(attachment.url);
    $row.find('.fallback-image-preview img').attr('src', attachment.url).show();
    $row.find('.remove-fallback-image').show();
  }

  // Selecionar imagem de fallback (Media Library)
  $(document).on('click', '.select-fallback-image', function(e){
    e.preventDefault();
    var $row = $(this).closest('tr');
    if (mediaFrame) mediaFrame.close();
    mediaFrame = wp.media({
      title: 'Escolher imagem de fallback',
      library: { type: 'image' },
      button: { text: 'Usar essa imagem' },
      multiple: false
    });
    mediaFrame.on('select', function(){
      var att = mediaFrame.state().get('selection').first().toJSON();
      updatePreview($row, att);
    });
    mediaFrame.open();
  });

  // Remover imagem de fallback
  $(document).on('click', '.remove-fallback-image', function(e){
    e.preventDefault();
    var $row = $(this).closest('tr');
    $row.find('.fallback-image-id').val('');
    $row.find('.fallback-image-url').val('');
    $row.find('.fallback-image-preview img').hide().attr('src','');
    $(this).hide();
  });

  // Adicionar nova regra
  $('#add-rule').on('click', function(e){
    e.preventDefault();
    var idx  = $('#rules tbody tr').length;
    var $tpl = $('#rules tbody tr').first().clone();

    $tpl.find('input,select').each(function(){
      var $field = $(this);
      var name = $field.attr('name');
      if (name) {
        name = name.replace(/ebvcf_rules\[\d+\]/, 'ebvcf_rules['+idx+']');
        $field.attr('name', name);
      }
      if ($field.is(':checkbox')) {
        $field.prop('checked', false);
      } else if ($field.is('[type="color"]')) {
        // mantém a cor default do template
      } else {
        $field.val('');
      }
    });

    $tpl.find('.fallback-image-preview img').hide().attr('src','');
    $tpl.find('.remove-fallback-image').hide();

    $('#rules tbody').append($tpl);
  });

  // Remover regra + renumerar + salvar rascunho via AJAX
  $(document).on('click', '.remove-rule', function(e){
    e.preventDefault();
    if (!confirm('Remover essa regra?')) return;

    $(this).closest('tr').remove();

    $('#rules tbody tr').each(function(i){
      $(this).find('input,select').each(function(){
        var name = $(this).attr('name');
        if (name) {
          $(this).attr('name', name.replace(/ebvcf_rules\[\d+\]/, 'ebvcf_rules['+i+']'));
        }
      });
    });

    // rascunho opcional — usa ajaxurl e o mesmo nonce do settings_fields
    var data = $('#ebvcf-form').serialize() + '&action=ebvcf_remove_rule';
    if (typeof ebvcfAdmin !== 'undefined' && ebvcfAdmin.nonce) {
      data += '&_wpnonce=' + encodeURIComponent(ebvcfAdmin.nonce);
    }
    if (typeof ajaxurl !== 'undefined' && ajaxurl) {
      $.post(ajaxurl, data);
    }
  });
});
