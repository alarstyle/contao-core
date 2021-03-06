<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * This is the tinyMCE (rich text editor) configuration file. Please visit
 * http://tinymce.moxiecode.com for more information.
 */
if ($GLOBALS['TL_CONFIG']['useRTE']):

?>
<script>window.tinymce || document.write('<script src="<?php echo TL_ASSETS_URL; ?>assets/tinymce4/tinymce.gzip.js">\x3C/script>')</script>
<script>
setTimeout(function() {
  window.tinymce && tinymce.init({
    skin: 'contao',
    selector: '#<?php echo $selector; ?>',
    language: '<?php echo \Contao\Backend::getTinyMceLanguage(); ?>',
    element_format: 'html',
    document_base_url: '<?php echo \Contao\Environment::get('base'); ?>',
    entities: '160,nbsp,60,lt,62,gt,173,shy',
    setup: function(editor) {
      editor.getElement().removeAttribute('required');
    },
    init_instance_callback: function(editor) {
      if (document.activeElement && document.activeElement.id && document.activeElement.id == editor.id) {
        editor.editorManager.get(editor.id).focus();
      }
    },
    file_browser_callback: function(field_name, url, type, win) {
      Backend.openModalBrowser(field_name, url, type, win);
    },
    templates: [
      <?php echo \Contao\Backend::getTinyTemplates(); ?>
    ],
    doctype: '<!DOCTYPE html PUBLIC \'-//W3C//DTD HTML 3.2//EN\'>',
    plugins: 'autosave charmap code fullscreen image link lists paste searchreplace tabfocus table template visualblocks',
    browser_spellcheck: true,
    tabfocus_elements: ':prev,:next',
    content_css: '<?php echo TL_PATH; ?>/system/themes/tinymce.css',
    extended_valid_elements: 'b/strong,i/em',
    menubar: 'file edit insert view format table',
    toolbar: 'link unlink | image | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | undo redo | code'
  });
}, 0);
</script>
<?php endif; ?>
