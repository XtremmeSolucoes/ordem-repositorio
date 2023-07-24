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
            
            <h5 class="card-title mt-2">Nome: <?php echo esc($cliente->nome); ?></h5>
            <p class="card-text">CPF: <?php echo esc($cliente->cpf); ?></p>
            <p class="card-text">Email: <?php echo esc($cliente->email); ?></p>
            <p class="card-text">Telefone: <?php echo esc($cliente->telefone); ?>&nbsp;&nbsp;&nbsp; <a href="tel:<?php echo esc($cliente->telefone); ?>" target="_blank"><i class="bi bi-telephone" style="font-size: 1.5rem;"></i></a></p>
            <p class="card-text">WhatsApp: <?php echo esc($cliente->telefone); ?>&nbsp;&nbsp;&nbsp;<a href="https://web.whatsapp.com/send?phone=<?php echo esc($cliente->telefone); ?>" target="_blank"> <i class="bi bi-whatsapp" style="font-size: 1.5rem;"></i></a></p>
            <p class="card-text">Endereço: <?php echo esc($cliente->endereco); ?></p>    
            <p class="card-text">Nº: <?php echo esc($cliente->numero); ?></p>    
            <p class="card-text">Bairro: <?php echo esc($cliente->bairro); ?></p>    
            <p class="card-text">Cidade: <?php echo esc($cliente->cidade); ?></p>    
            <p class="card-text">Estado: <?php echo esc($cliente->estado); ?></p>    
            <p class="card-text">Criado: <?php echo $cliente->criado_em->humanize(); ?></p>
            <p class="card-text">Atualizado: <?php echo $cliente->atualizado_em->humanize(); ?></p>

            <!-- Example single danger button -->
            <div class="btn-group">
                <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo site_url("clientes/editar/$cliente->id"); ?>">Editar Cliente</a>
                    
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo site_url("clientes/historico/$cliente->id"); ?>">Histórico do Cliente</a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="<?php echo site_url("clientes/excluir/$cliente->id"); ?>">Excluir Cliente</a>

                </div>
            </div>

            <a href="<?php echo site_url("clientes"); ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>

        </div> <!-- ./ block -->
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- aqui os scripts da view  -->
<?= $this->endSection() ?>