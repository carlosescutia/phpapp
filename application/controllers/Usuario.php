<?php
class Usuario extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('usuario_model');
        $this->load->model('rol_model');
        $this->load->model('organizacion_model');
        $this->load->model('acceso_sistema_model');
        $this->load->model('opcion_sistema_model');
        $this->load->model('bitacora_model');
        $this->load->model('parametro_sistema_model');
    }

    public function get_userdata()
    {
        $id_rol = $this->session->userdata('id_rol');
        $data['id_usuario'] = $this->session->userdata('id_usuario');
        $data['id_organizacion'] = $this->session->userdata('id_organizacion');
        $data['nom_organizacion'] = $this->session->userdata('nom_organizacion');
        $data['id_rol'] = $id_rol;
        $data['nom_usuario'] = $this->session->userdata('nom_usuario');
        $data['error'] = $this->session->flashdata('error');
        $data['accesos_sistema'] = explode(',', $this->acceso_sistema_model->get_accesos_sistema_rol($id_rol)['accesos']);
        $data['opciones_sistema'] = $this->opcion_sistema_model->get_opciones_sistema();
        return $data;
    }

    public function get_system_params()
    {
        $data['nom_sitio_corto'] = $this->parametro_sistema_model->get_parametro_sistema_nombre('nom_sitio_corto');
        $data['nom_sitio_largo'] = $this->parametro_sistema_model->get_parametro_sistema_nombre('nom_sitio_largo');
        $data['nom_org_sitio'] = $this->parametro_sistema_model->get_parametro_sistema_nombre('nom_org_sitio');
        $data['anio_org_sitio'] = $this->parametro_sistema_model->get_parametro_sistema_nombre('anio_org_sitio');
        $data['tel_org_sitio'] = $this->parametro_sistema_model->get_parametro_sistema_nombre('tel_org_sitio');
        $data['correo_org_sitio'] = $this->parametro_sistema_model->get_parametro_sistema_nombre('correo_org_sitio');
        $data['logo_org_sitio'] = $this->parametro_sistema_model->get_parametro_sistema_nombre('logo_org_sitio');
        return $data;
    }

    public function index()
    {
        if ($this->session->userdata('logueado')) {
            $data = [];
            $data += $this->get_userdata();
            $data += $this->get_system_params();

            if ($data['id_rol'] != 'adm') {
                redirect('admin');
            }

            $data['usuarios'] = $this->usuario_model->get_usuarios();

            $this->load->view('templates/admheader', $data);
            $this->load->view('templates/dlg_borrar');
            $this->load->view('catalogos/usuario/lista', $data);
            $this->load->view('templates/footer', $data);
        } else {
            redirect('admin/login');
        }
    }

    public function detalle($id_usuario)
    {
        if ($this->session->userdata('logueado')) {
            $data = [];
            $data += $this->get_userdata();
            $data += $this->get_system_params();

            if ($data['id_rol'] != 'adm') {
                redirect('admin');
            }

            $data['usuario'] = $this->usuario_model->get_usuario($id_usuario);
            $data['roles'] = $this->rol_model->get_roles();
            $data['organizaciones'] = $this->organizacion_model->get_organizaciones();

            $this->load->view('templates/admheader', $data);
            $this->load->view('catalogos/usuario/detalle', $data);
            $this->load->view('templates/footer', $data);
        } else {
            redirect('admin/login');
        }
    }

    public function nuevo()
    {
        if ($this->session->userdata('logueado')) {
            $data = [];
            $data += $this->get_userdata();
            $data += $this->get_system_params();

            if ($data['id_rol'] != 'adm') {
                redirect('admin');
            }

            $data['roles'] = $this->rol_model->get_roles();
            $data['organizaciones'] = $this->organizacion_model->get_organizaciones();

            $this->load->view('templates/admheader', $data);
            $this->load->view('catalogos/usuario/nuevo', $data);
            $this->load->view('templates/footer', $data);
        } else {
            redirect('admin/login');
        }
    }

    public function guardar($id_usuario=null)
    {
        if ($this->session->userdata('logueado')) {

            $usuario = $this->input->post();
            if ($usuario) {

                if ($id_usuario) {
                    $accion = 'modificó';
                } else {
                    $accion = 'agregó';
                }
                // guardado
                $data = array(
                    'id_organizacion' => $usuario['id_organizacion'],
                    'id_rol' => $usuario['id_rol'],
                    'nom_usuario' => $usuario['nom_usuario'],
                    'usuario' => $usuario['usuario'],
                    'password' => $usuario['password'],
                    'activo' => empty($usuario['activo']) ? '0' : $usuario['activo']
                );
                $id_usuario = $this->usuario_model->guardar($data, $id_usuario);

                // registro en bitacora
                $organizacion = $this->organizacion_model->get_organizacion($usuario['id_organizacion']);
                $separador = ' -> ';
                $usuario = $this->session->userdata('usuario');
                $nom_usuario = $this->session->userdata('nom_usuario');
                $nom_organizacion = $this->session->userdata('nom_organizacion');
                $entidad = 'usuario';
                $valor = $id_usuario ." ". $usuario['nom_usuario'] . $separador . $organizacion['nom_organizacion'];
                $data = array(
                    'fecha' => date("Y-m-d"),
                    'hora' => date("H:i"),
                    'origen' => $_SERVER['REMOTE_ADDR'],
                    'usuario' => $usuario,
                    'nom_usuario' => $nom_usuario,
                    'nom_organizacion' => $nom_organizacion,
                    'accion' => $accion,
                    'entidad' => $entidad,
                    'valor' => $valor
                );
                $this->bitacora_model->guardar($data);

            }
            redirect('usuario');

        } else {
            redirect('admin/login');
        }
    }

    public function eliminar($id_usuario)
    {
        if ($this->session->userdata('logueado')) {

            // registro en bitacora
            $datos_usuario = $this->usuario_model->get_usuario($id_usuario);
            $separador = ' -> ';
            $usuario = $this->session->userdata('usuario');
            $nom_usuario = $this->session->userdata('nom_usuario');
            $nom_organizacion = $this->session->userdata('nom_organizacion');
            $accion = 'eliminó';
            $entidad = 'usuario';
            $valor = $id_usuario ." ". $datos_usuario['nom_usuario'] . $separador . $datos_usuario['nom_organizacion'];
            $data = array(
                'fecha' => date("Y-m-d"),
                'hora' => date("H:i"),
                'origen' => $_SERVER['REMOTE_ADDR'],
                'usuario' => $usuario,
                'nom_usuario' => $nom_usuario,
                'nom_organizacion' => $nom_organizacion,
                'accion' => $accion,
                'entidad' => $entidad,
                'valor' => $valor
            );
            $this->bitacora_model->guardar($data);

            // eliminado
            $this->usuario_model->eliminar($id_usuario);

            redirect('usuario');
        } else {
            redirect('admin/login');
        }
    }
}
