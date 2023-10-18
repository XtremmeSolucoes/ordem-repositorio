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
    <div class="col-lg-12">
        <div class="block">

            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Detalhes da Ordem</button>
                </li>
                <?php if (isset($ordem->transacao)) : ?>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Transações da Ordem</button>
                    </li>

                <?php endif; ?>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="user-block text-center">

                        <div class="user-title mb-4">

                            <h4 class="card-title mt-2"><?php echo esc($ordem->nome); ?></h4>
                            <span>Ordem: <?php echo esc($ordem->codigo); ?></span>

                        </div>

                        <p class="contributions mt-0"><?php echo $ordem->exibeSituacao(); ?></p>
                        <p class="contributions mt-0">Aberta por:<b> <?php echo esc($ordem->usuario_abertura); ?></b></p>
                        <p class="contributions mt-0">Responsável Tecnico:<b> <?php echo esc($ordem->usuario_responsavel !== null ? $ordem->usuario_responsavel : 'Não Definido'); ?></b></p>

                        <?php if ($ordem->situacao === 'encerrada') : ?>

                            <p class="contributions mt-0">Encerrada por:<b> <?php echo esc($ordem->usuario_encerramento); ?></b></p>

                        <?php endif; ?>

                        <p class="card-text">Criado: <?php echo $ordem->criado_em->humanize(); ?></p>
                        <p class="card-text">Atualizado: <?php echo $ordem->atualizado_em->humanize(); ?></p>

                        <hr class="border-secondary">

                        <?php if ($ordem->ordens === null) : ?>

                            <div class="contributions py-3">

                                <p>Nenhum item foi adicionado à Ordem</p>

                                <?php if ($ordem->situacao === 'aberta') : ?>

                                    <a class="btn btn-outline-info btn-sm" href="<?php echo site_url("ordensordens/ordens/$ordem->codigo") ?>">Adiconar Item</a>

                                <?php endif; ?>

                            </div>

                        <?php else : ?>

                            ------------------------ TEM ordens --------------------------------

                        <?php endif; ?>

                    </div>
                </div>

                <?php if (isset($ordem->transacao)) : ?>

                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                        Transações
                    </div>

                <?php endif; ?>
            </div>

            <!-- Example single danger button -->
            <div class="btn-group">
                <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <?php if ($ordem->situacao === 'aberta') : ?>

                        <a class="dropdown-item" href="<?php echo site_url("ordens/editar/$ordem->codigo"); ?>">Editar Ordem</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo site_url("ordens/encerrar/$ordem->codigo"); ?>">Encerrar Ordem</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo site_url("ordensitens/itens/$ordem->codigo"); ?>">Gerenciar itens da Ordem</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo site_url("ordens/responsavel/$ordem->codigo"); ?>">Definir técnico responsável</a>
                        <div class="dropdown-divider"></div>

                    <?php endif; ?>

                    <a class="dropdown-item" href="<?php echo site_url("ordensevidencias/evidencias/$ordem->codigo"); ?>">Evidências da Ordem</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo site_url("ordens/email/$ordem->codigo"); ?>">Enviar por E-mail</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo site_url("ordens/gerapdf/$ordem->codigo"); ?>">Gerar PDF</a>
                    <div class="dropdown-divider"></div>

                    <?php if ($ordem->deletado_em === null) : ?>

                        <a class="dropdown-item" href="<?php echo site_url("ordens/excluir/$ordem->codigo"); ?>">Excluir Ordem</a>

                    <?php endif; ?>

                </div>
            </div>

            <a href="<?php echo site_url("ordens"); ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>

        </div> <!-- ./ block -->
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- aqui os scripts da view  -->
<?= $this->endSection() ?>