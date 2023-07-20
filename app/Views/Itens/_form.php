<?php if ($item->id === null) : ?>
    <div>
        <label>Este é um Item do tipo: </label>
    </div>

    <div class="custom-control custom-radio custom-control-inline mb-3">
        <input type="radio" id="produto" name="tipo" value="produto" class="custom-control-input" checked>
        <label class="custom-control-label" for="produto"><i class="fa fa-archive text-success">&nbsp; Novo Produto</i></label>
    </div>
    <div class="custom-control custom-radio custom-control-inline mb-3">
        <input type="radio" id="servico" name="tipo" value="serviço" class="custom-control-input">
        <label class="custom-control-label" for="servico"><i class="fa fa-wrench text-white">&nbsp; Novo Serviço</i></label>
    </div>
<?php endif; ?>
<div class="form-row">

    <div class="form-group col-md-12">
        <label class="form-control-label">Nome</label>
        <input type="text" name="nome" placeholder="Insira o nome do Item" class="form-control" value="<?php echo esc($item->nome); ?>">
    </div>


</div>
<div class="form-row">
    <div class="servico form-group col-md-5">
        <label class="form-control-label">Marca</label>
        <input type="text" name="marca" placeholder="Insira a marca do Item" class="form-control" value="<?php echo esc($item->marca); ?>">
    </div>
    <div class="servico form-group col-md-5">
        <label class="form-control-label">Modelo</label>
        <input type="text" name="modelo" placeholder="Insira o modelo do Item" class="form-control" value="<?php echo esc($item->modelo); ?>">
    </div>
    <div class="servico form-group col-md-2">
        <label class="form-control-label">Estoque</label>
        <input type="number" name="estoque" placeholder="Estoque" class="form-control" value="<?php echo esc($item->estoque); ?>">
    </div>
</div>
<div class="form-row">
    <div class="servico form-group col-md-4">
        <label class="form-control-label">Preço de Custo</label>
        <input type="text" name="preco_custo" placeholder="Valor de Custo" class="form-control money" value="<?php echo esc($item->preco_custo); ?>">
    </div>
    <div class="form-group col-md-4">
        <label class="form-control-label">Preço de Venda</label>
        <input type="text" name="preco_venda" placeholder="Valor de Venda" class="form-control money" value="<?php echo esc($item->preco_venda); ?>">
    </div>
</div>
<div class="form-row">
    <div class="form-group col-md-12">
        <label class="form-control-label">Descrição</label>
        <textarea name="descricao" placeholder="Insira a descrição do Item" class="form-control" rows="5"><?php echo esc($item->descricao); ?></textarea>
    </div>

</div>

<div class="servico custom-control custom-checkbox mb-2">

    <input type="hidden" name="controla_estoque" value="0">

    <input type="checkbox" name="controla_estoque" value="1" class="custom-control-input" id="controla_estoque" <?php if ($item->controla_estoque == true) : ?> checked <?php endif; ?>>
    <label class="custom-control-label" for="controla_estoque">Controle de Estoque</label>

</div>
<div class="custom-control custom-checkbox mb-2">

    <input type="hidden" name="ativo" value="0">

    <input type="checkbox" name="ativo" value="1" class="custom-control-input" id="ativo" <?php if ($item->ativo == true) : ?> checked <?php endif; ?>>
    <label class="custom-control-label" for="ativo">Item ativo</label>

</div>