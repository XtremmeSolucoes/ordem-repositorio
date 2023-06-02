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
        <div class="user-block block">
            <?php if (empty($permissoesDisponiveis)) : ?>

                <p class="contributions mt-0">Esse grupo já possui todas permissões de acesso Disponiveis!</p>

            <?php else : ?>
        </div>

        <?php echo form_open('/', ['id' => 'form'], ['id' => "$grupo->id"]) ?>
        <div class="form-group">
            <label class="form-control-label">Escolha uma ou mais permissões!</label>

            <select name="permissao_id[]" class="form-control" multiple>
                <option value="">Escolha......</option>
                <?php foreach ($permissoesDisponiveis as $permissao) : ?>

                    <option value="<?php echo $permissao->id; ?>"><?php echo esc($permissao->nome); ?></option>

                <?php endforeach; ?>

            </select>

        </div>
        <div class="form-group mt-5 mb-2">

            <input id="btn-salvar" type="submit" value="Salvar" class="btn btn-danger btn-sm mr-2">
            <a href="<?php echo site_url("grupos/exibir/$grupo->id"); ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>

        </div>

        <?php echo form_close(); ?>
    <?php endif; ?>

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
                            <?php foreach ($grupo->permissoes as $permissao) : ?>
                                <tr>
                                    <td><?php echo $permissao->nome; ?></td>
                                    <td><a href="#" class="btn btn-sm btn-danger">Excluir</a></td>
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