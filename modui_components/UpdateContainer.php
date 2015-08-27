<?php

class UpdateContainer extends NormalContainer {

    public function get_update_script($name){
        return 'function(selector, update){$(document).on("click", "#" + selector + "_update", update);}';
    }

}
