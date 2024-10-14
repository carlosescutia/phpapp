<?php
class Opcion_sistema_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_opciones_sistema() {
        $sql = 'select * from opcion_sistema order by codigo;';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_opcion_sistema($id_opcion_sistema) {
        $sql = 'select * from opcion_sistema where id_opcion_sistema = ?;';
        $query = $this->db->query($sql, array($id_opcion_sistema));
        return $query->row_array();
    }

    public function get_opcion_sistema_codigo($codigo) {
        $sql = 'select * from opcion_sistema where codigo = ?;';
        $query = $this->db->query($sql, array($codigo));
        return $query->row_array();
    }

    public function guardar($data, $id_opcion_sistema)
    {
        if ($id_opcion_sistema) {
            $this->db->where('id_opcion_sistema', $id_opcion_sistema);
            $this->db->update('opcion_sistema', $data);
            $id = $id_opcion_sistema;
        } else {
            $this->db->insert('opcion_sistema', $data);
            $id = $this->db->insert_id();
        }
        return $id;
    }

    public function eliminar($id_opcion_sistema)
    {
        $this->db->where('id_opcion_sistema', $id_opcion_sistema);
        $result = $this->db->delete('opcion_sistema');
        return $result;
    }

}
