<?php
class Acceso_sistema extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('funciones_sistema');
        $this->load->model('acceso_sistema_model');
        $this->load->model('opcion_sistema_model');
        $this->load->model('rol_model');
    }

    public function index()
    {
        if ($this->session->userdata('logueado')) {
            $data = [];
            $data += $this->funciones_sistema->get_userdata();
            $data += $this->funciones_sistema->get_system_params();

            if ($data['id_rol'] != 'adm') {
                redirect(base_url() . 'admin');
            }

            $data['ed_accesos_sistema'] = $this->acceso_sistema_model->get_accesos_sistema();

            $this->load->view('templates/admheader', $data);
            $this->load->view('templates/dlg_borrar');
            $this->load->view('catalogos/acceso_sistema/lista', $data);
            $this->load->view('templates/footer', $data);
        } else {
            redirect(base_url() . 'admin/login');
        }
    }

    public function nuevo()
    {
        if ($this->session->userdata('logueado')) {
            $data = [];
            $data += $this->funciones_sistema->get_userdata();
            $data += $this->funciones_sistema->get_system_params();

            if ($data['id_rol'] != 'adm') {
                redirect(base_url() . 'admin');
            }

            $data['opciones_sistema'] = $this->opcion_sistema_model->get_opciones_sistema();
            $data['roles'] = $this->rol_model->get_roles();

            $this->load->view('templates/admheader', $data);
            $this->load->view('catalogos/acceso_sistema/nuevo', $data);
            $this->load->view('templates/footer', $data);
        } else {
            redirect(base_url() . 'admin/login');
        }
    }

    public function guardar($id_acceso=null)
    {
        if ($this->session->userdata('logueado')) {

            $acceso_sistema = $this->input->post();
            if ($acceso_sistema) {

                if ($id_acceso) {
                    $accion = 'modificó';
                } else {
                    $accion = 'agregó';
                }
                // guardado
                $data = array(
                    'codigo' => $acceso_sistema['codigo'],
                    'id_rol' => $acceso_sistema['id_rol']
                );
                $id_acceso = $this->acceso_sistema_model->guardar($data, $id_acceso);

                // registro en bitacora
                $opcion = $this->opcion_sistema_model->get_opcion_sistema_codigo($acceso_sistema['codigo']);
                $rol = $this->rol_model->get_rol($acceso_sistema['id_rol']);
                $separador = ' -> ';
                $entidad = 'acceso_sistema';
                $valor = $opcion['codigo'] . " " . $opcion['nombre'] . $separador . $rol['nombre'];
                $this->funciones_sistema->registro_bitacora($accion, $entidad, $valor);

            }
            redirect(base_url() . 'acceso_sistema');

        } else {
            redirect(base_url() . 'admin/login');
        }
    }

    public function eliminar($id_acceso)
    {
        if ($this->session->userdata('logueado')) {

            // registro en bitacora
            $acceso = $this->acceso_sistema_model->get_acceso_sistema($id_acceso);
            $opcion = $this->opcion_sistema_model->get_opcion_sistema_codigo($acceso['codigo']);
            $rol = $this->rol_model->get_rol($acceso['id_rol']);
            $separador = ' -> ';
            $accion = 'eliminó';
            $entidad = 'acceso_sistema';
            $valor = $opcion['codigo'] . " " . $opcion['nombre'] . $separador . $rol['nombre'];
            $this->funciones_sistema->registro_bitacora($accion, $entidad, $valor);

            // eliminado
            $this->acceso_sistema_model->eliminar($id_acceso);

            redirect(base_url() . 'acceso_sistema');
        } else {
            redirect(base_url() . 'admin/login');
        }
    }

}
