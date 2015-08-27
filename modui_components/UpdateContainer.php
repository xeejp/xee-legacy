<?php

class UpdateContainer extends NameContainer {

    public function get_update_script($name){
        return 'function(selector, update){$(document).on("click", "#" + selector + "_update", update);}';
    }

}
