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
    <div class="col-lg-4">
        <div class="user-block block">

            <?php if ($item->tipo === 'produto') : ?>

                <div class="text-center">
                    <?php if ($item->imagem == null) : ?>

                        <img src="<?php echo site_url('recursos/img/item_sem_imagem.png'); ?>" class="card-img-top" style="width: 90%;" alt="Item sem imagem">

                    <?php else : ?>

                        <img src="<?php echo site_url("itens/imagem/$item->imagem"); ?>" class="card-img-top" style="width: 90%;" alt="<?php echo esc($item->nome); ?>">

                    <?php endif; ?>

                    <a href="<?php echo site_url("itens/editarimagem/$item->id"); ?>" class="btn btn-outline-primary btn-sm mt-3">Alterar Imagem</a>

                </div>

                <hr class="border-secondary">

            <?php endif; ?>

            <h5 class="card-title mt-2">Nome: <?php echo esc($item->nome); ?></h5>
            <p class="contributions mt-0"><?php echo $item->exibeTipo(); ?></p>
            <p class="contributions mt-0"><?php echo $item->exibeSituacao(); ?></p>
            <p class="contributions mt-0">Estoque: <?php echo $item->exibeEstoque(); ?></p>
            <p class="contributions mt-0">
                <a class="btn btn-sm" href="<?php echo site_url("itens/codigobarras/$item->id") ?>" target="_blank">Ver código de Barras do Item</a>
            </p>
            <p class="card-text">Criado: <?php echo $item->criado_em->humanize(); ?></p>
            <p class="card-text">Atualizado: <?php echo $item->atualizado_em->humanize(); ?></p>

            <!-- Example single danger button -->
            <div class="btn-group">
                <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo site_url("itens/editar/$item->id"); ?>">Editar Item</a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="<?php echo site_url("itens/excluir/$item->id"); ?>">Excluir Item</a>

                </div>
            </div>

            <a href="<?php echo site_url("itens"); ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>

        </div> <!-- ./ block -->
    </div>
    <div class="col-lg-8">
        <div class="user-block block">

            <p class="contributions"> HISTÓRICO DE ALTERAÇOES DO ITEM</p>

            <?php if (isset($item->historico) === false) : ?>
                <p>
                    <span class="text-white">Item sem histórico!</span>
                </p>
            <?php else : ?>

                <div id="accordion">
                    <?php foreach ($item->historico as $key => $historico) : ?>
                        <div class="card">
                            <div class="card-header" id="heading-<?php echo $key; ?>">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-<?php echo $key; ?>" aria-expanded="true" aria-controls="collapseOne">
                                        Em <?php echo date('d/m/Y H:i', strtotime($historico['criado_em'])); ?>
                                    </button>
                                </h5>
                            </div>

                            <div id="collapse-<?php echo $key; ?>" class="collapse <?php echo($key === 0 ? 'show' : ''); ?>" aria-labelledby="heading-<?php echo $key; ?>" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Em <?php echo date('d/m/Y H:i', strtotime($historico['criado_em'])); ?></p>

                                    <?php foreach ($historico['atributos_alterados'] as $evento) : ?>
                                        <p><?php echo $evento; ?></p>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- aqui os scripts da view  -->
<?= $this->endSection() ?>