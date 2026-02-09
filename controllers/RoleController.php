<?php 

class RoleController {

    public function index(){
        $roles = Role::all();

        View::render('pages/roles/index',[
            'title' => 'Roles',
            'roles' => $roles,
        ]);
    }

}