<?= $this->extend('Layout/principal') ?>


<?= $this->section('titulo') ?>
<?= $titulo ?>
<?= $this->endSection() ?>

<?= $this->section('estilos') ?>

<!-- aqui os estilos da view -->
<link rel="stylesheet" href="<?php echo site_url('recursos/vendor/fullcalendar/fullcalendar.min.css'); ?>">
<link rel="stylesheet" href="<?php echo site_url('recursos/vendor/fullcalendar/toastr.min.css'); ?>">

<style>

   /* Altera a cor de fundo do eventos */
   .fc-event, .fc-event-dot {
      background-color: #343a40 !important;
   }
</style>

<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div id="calendario" class="container-fluid">

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<!-- aqui os scripts da view  -->
<script src="<?php echo site_url('recursos/vendor/fullcalendar/fullcalendar.min.js'); ?>"></script>
<script src="<?php echo site_url('recursos/vendor/fullcalendar/toastr.min.js'); ?>"></script>
<script src="<?php echo site_url('recursos/vendor/fullcalendar/moment.min.js'); ?>"></script>

<script>
   $(document).ready(function() {

      var calendario = $("#calendario").fullCalendar({

         header: {

            left: 'prev, next today',
            center: 'title',
            right: 'month',
         },
         height: 500,
         editable: true,
         events: '<?php echo site_url('eventos/eventos') ?>',
         displayEventTime: false,
         selectable: true,
         selectHelper: true,
         select: function(start, end, allDay) {
            var title = prompt('Informe o Título do Evento');
            if (title) {

               var start = $.fullCalendar.formatDate(start, 'Y-MM-DD'); //formatação do moment.js
               var end = $.fullCalendar.formatDate(end, 'Y-MM-DD'); //formatação do moment.js

               $.ajax({

                  url: '<?php echo site_url('eventos/cadastrar') ?>',
                  type: 'GET',
                  data: {
                     title: title,
                     start: start,
                     end: end,
                  },

                  success: function(response) {

                     exibeMensagem('Evento criado com sucesso!');

                     calendario.fullCalendar('renderEvent', {

                        id: response.id,
                        title: title,
                        start: start,
                        end: end,
                        allDay: allDay,

                     }, true);

                     calendario.fullCalendar('unselect');

                  }, //fim do success

               }); //fim ajax do cadastro

            } // fim do title
         },

         //atualiza eventos
         eventDrop: function(event, delta, revertFunc) {

            //verifica se o evento está atrelado a algo
            if (event.conta_id || event.ordem_id) {

               alert('Não é possível alterar um evento, atrelado a uma conta ou ordem de serviço!');
               revertFunc();

            } else {

               //atualiza o evento 
               var start = $.fullCalendar.formatDate(event.start, 'Y-MM-DD'); //formatação do moment.js
               var end = $.fullCalendar.formatDate(event.end, 'Y-MM-DD'); //formatação do moment.js

               $.ajax({

                  url: '<?php echo site_url('eventos/atualizar/') ?>' + event.id, //ID do evento que será atualizado
                  type: 'GET',
                  data: {
                     start: start,
                     end: end,
                  },

                  success: function(response) {

                     exibeMensagem('Evento atualizado com sucesso!');

                  }, //fim metodo atulizar o evento do success

               }); //fim ajax da atualização

            } // fim do else da atualização

         }, // FIM atualiza eventos

         //ExCLUSÃO EVENTOS
         eventClick: function(event) {

            if (event.conta_id || event.ordem_id) {

               alert(event.title);

            } else {

               var exibEvento = confirm(event.title + '\r\n\r' + 'Gostaria de excluir esse evento?');

               if (exibEvento) {

                  var confirmaExclusao = confirm("Tem certeza?");

                  if (confirmaExclusao) {

                     $.ajax({

                        url: '<?php echo site_url('eventos/excluir') ?>', 
                        type: 'GET',
                        data: {
                           id: event.id,
                        },

                        success: function(response) {

                           calendario.fullCalendar('removeEvents', event.id);
                           exibeMensagem('Evento removido com sucesso!');

                        }, //fim metodo excluir o evento do success

                     }); //fim ajax da exclusão

                  } //FIM confirmaExclusao

               } // FIM exibeEvento

            } // FIM else EXCLUSÃO

         }, // FIM eventClick

      });

   });

   function exibeMensagem(mensagem) {
      toastr.success(mensagem, 'Evento');
   }
</script>

<?= $this->endSection() ?>