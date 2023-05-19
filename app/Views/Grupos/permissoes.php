<?= $this->extend('Layout/principal') ?>


<?= $this->section('titulo') ?>
<?= $titulo ?>
<?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!-- aqui os estilos da view -->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>
<!-- aqui os conteudos da view  -->
<div class="row">
    <div class="col-lg-8">

    </div>
    <div class="col-lg-4">
        <div class="user-block block">

            <?php if (empty($grupo->permissoes)) : ?>

                <p class="contributions text-warning  mt-0">Esse grupo ainda não possui permissões de acesso!</p>

            <?php else : ?>

                <div class="table-responsive">

                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Permissão</th>
                                <th>Excluir</th>                               
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grupo->permissoes as $permissao): ?>                           
                            <tr>
                                <td><?php echo $permissao->nome; ?></td> 
                                <td></td>                              
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="mt-3 ml-2">
                    <?php echo $grupo->pager->links(); ?>
                    </div>
                </div>


            <?php endif; ?>

        </div> <!-- ./ block -->
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- aqui os scripts da view  -->
<?= $this->endSection() ?>