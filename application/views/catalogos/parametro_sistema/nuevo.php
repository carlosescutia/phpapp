<main role="main" class="ml-sm-auto px-4">

    <form method="post" action="<?= base_url() ?>parametro_sistema/guardar">

        <div class="col-md-12 mb-3 pb-2 pt-3 border-bottom">
            <div class="row">
                <div class="col-md-10">
                    <h1 class="h2">Nuevo parámetro del sistema</h1>
                </div>
                <div class="col-md-2 text-end">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group row">
                <label for="nombre" class="col-sm-2 col-form-label">Parámetro</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="nombre" id="nombre">
                </div>
            </div>
            <div class="form-group row">
                <label for="valor" class="col-sm-2 col-form-label">Valor</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="valor" id="valor">
                </div>
            </div>
        </div>

    </form>


    <hr />

    <div class="form-group row">
        <div class="col-sm-10">
            <a href="<?=base_url()?>parametro_sistema" class="btn btn-secondary boton">Volver</a>
        </div>
    </div>

</main>