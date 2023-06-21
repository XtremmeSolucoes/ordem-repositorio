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
            
            <h5 class="card-title mt-2">Razão Social: <?php echo esc($fornecedor->razao); ?></h5>
            <p class="card-text">CNPJ: <?php echo esc($fornecedor->cnpj); ?></p>
            <p class="card-text">Telefone: <?php echo esc($fornecedor->telefone); ?>&nbsp;&nbsp;&nbsp; <a href="tel:<?php echo esc($fornecedor->telefone); ?>" target="_blank"><i class="bi bi-telephone" style="font-size: 1.5rem;"></i></a></p>
            <p class="card-text">WhatsApp: <?php echo esc($fornecedor->telefone); ?>&nbsp;&nbsp;&nbsp;<a href="https://web.whatsapp.com/send?phone=<?php echo esc($fornecedor->telefone); ?>" target="_blank"> <i class="bi bi-whatsapp" style="font-size: 1.5rem;"></i></a></p>
            <p class="card-text">Endereço: <?php echo esc($fornecedor->endereco); ?></p>    
            <p class="card-text">Nº: <?php echo esc($fornecedor->numero); ?></p>    
            <p class="card-text">Bairro: <?php echo esc($fornecedor->bairro); ?></p>    
            <p class="card-text">Cidade: <?php echo esc($fornecedor->cidade); ?></p>    
            <p class="card-text">Estado: <?php echo esc($fornecedor->estado); ?></p>    
            <p class="card-text">Criado: <?php echo $fornecedor->criado_em->humanize(); ?></p>
            <p class="card-text">Atualizado: <?php echo $fornecedor->atualizado_em->humanize(); ?></p>

            <!-- Example single danger button -->
            <div class="btn-group">
                <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo site_url("fornecedores/editar/$fornecedor->id"); ?>">Editar Fornecedor</a>

                    <div class="dropdown-divider"></div>
                    
                    <a class="dropdown-item" href="<?php echo site_url("fornecedores/notas/$fornecedor->id"); ?>">Gerenciar as notas fiscais</a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="<?php echo site_url("fornecedores/excluir/$fornecedor->id"); ?>">Excluir Fornecedor</a>

                </div>
            </div>

            <a href="<?php echo site_url("fornecedores"); ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>

        </div> <!-- ./ block -->
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- aqui os scripts da view  -->
<?= $this->endSection() ?>