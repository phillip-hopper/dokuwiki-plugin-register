<?php
/**
 * Name: RegisterOverride.php
 * Description: A Dokuwiki action plugin to override the default register behavior.
 *
 * Author: Phil Hopper
 * Date:   2015-02-24
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class action_plugin_door43register_RegisterOverride extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('TPL_ACT_RENDER', 'BEFORE', $this, 'handle_register_action');
    }

    /**
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */
    public function handle_register_action(Doku_Event &$event, /** @noinspection PhpUnusedParameterInspection */ $param) {

        if ($event->data !== 'register') return;

        //no other action handlers needed
        $event->stopPropagation();
        $event->preventDefault();

        self::override_html_register();
    }

    static function override_html_register(){
        global $lang;
        global $conf;
        global $INPUT;

        $base_attrs = array('size'=>50,'required'=>'required');
        $email_attrs = $base_attrs + array('type'=>'email','class'=>'edit');

        print self::override_locale_xhtml('register');
        print '<div class="centeralign">'.NL;
        $form = new Doku_Form(array('id' => 'dw__register'));
        $form->startFieldset($lang['btn_register']);
        $form->addHidden('do', 'register');
        $form->addHidden('save', '1');
        $form->addElement(form_makeTextField('login', $INPUT->post->str('login'), $lang['user'], '', 'block', $base_attrs));
        if (!$conf['autopasswd']) {
            $form->addElement(form_makePasswordField('pass', $lang['pass'], '', 'block', $base_attrs));
            $form->addElement(form_makePasswordField('passchk', $lang['passchk'], '', 'block', $base_attrs));
        }
        $form->addElement(form_makeTextField('fullname', $INPUT->post->str('fullname'), $lang['fullname'], '', 'block', $base_attrs));
        $form->addElement(form_makeField('email','email', $INPUT->post->str('email'), $lang['email'], '', 'block', $email_attrs));
        $form->addElement(form_makeButton('submit', '', $lang['btn_register']));
        $form->endFieldset();
        html_form('register', $form);

        print '</div>'.NL;
    }

    static function override_locale_xhtml($id){
        //fetch parsed locale
        $html = p_cached_output(self::override_localeFN($id));
        return $html;
    }

    static function override_localeFN($id,$ext='txt'){
        global $conf;
        $file = DOKU_CONF.'lang/'.$conf['lang'].'/'.$id.'.'.$ext;
        if(!@file_exists($file)){
            $file = DOKU_CONF.'lang/en/'.$id.'.'.$ext;
        }
        return $file;
    }
}
