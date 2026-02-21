/**
 * CS Express - Funções para manipulação de endereços
 * Arquivo para ser incluído via Vite
 */

// Namespace para funções de endereço
window.CSExpress = window.CSExpress || {};
CSExpress.Endereco = {
    /**
     * Aplica máscaras nos campos de endereço e contato
     */
    setMascaras: function() {
        if ($.fn.mask) {
            $('#cep').mask('00000-000');
            $('#telefone').mask('(00) 00000-0000');
        } else {
            console.warn('Plugin jQuery Mask não encontrado');
        }
    },

    /**
     * Busca dados do CEP diretamente da API ViaCEP
     */
    buscarCep: function() {
        var cep = $('#cep').val().replace(/\D/g, '');
        console.log('Buscando CEP:', cep);

        if (cep.length !== 8) {
            alert('CEP inválido');
            return;
        }

        // Mostrar loading
        $('#buscarCep').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        // Limpar campos
        $('#endereco').val('');
        $('#complemento').val('');
        $('#cidade').val('');
        $('#uf').val('');

        // Usando ViaCEP diretamente (sem passar pelo backend)
        $.getJSON(`https://viacep.com.br/ws/${cep}/json/`)
            .done(function(data) {
                console.log('CEP encontrado:', data);

                if (!data.erro) {
                    $('#endereco').val(data.logradouro);
                    $('#complemento').val(data.complemento);
                    $('#cidade').val(data.localidade);
                    $('#uf').val(data.uf);
                } else {
                    alert('CEP não encontrado');
                }
            })
            .fail(function(xhr) {
                console.error('Erro ao buscar CEP:', xhr);
                alert('Erro ao buscar CEP');
            })
            .always(function() {
                // Restaurar botão
                $('#buscarCep').prop('disabled', false).html('<i class="fas fa-search"></i>');
            });
    },

    /**
     * Inicializa os manipuladores de eventos para os campos de endereço
     */
    initHandlers: function() {
        console.log('Inicializando handlers de endereço...');
        this.setMascaras();

        var self = this;

        // Evento do botão de buscar CEP
        $(document).on('click', '#buscarCep', function(e) {
            console.log('Botão de buscar CEP clicado');
            e.preventDefault();
            self.buscarCep();
        });

        // Buscar CEP automaticamente quando o campo perder o foco
        $('#cep').on('blur', function() {
            console.log('Campo CEP perdeu o foco');
            if ($(this).val().replace(/\D/g, '').length === 8) {
                self.buscarCep();
            }
        });

        console.log('Handlers de endereço inicializados, botão CEP encontrado:', $('#buscarCep').length);
    }
};

// Inicializar quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    console.log('Arquivo endereco.js carregado via Vite');
    // Certifique-se de que o jQuery está disponível
    if (typeof jQuery !== 'undefined') {
        CSExpress.Endereco.initHandlers();
    } else {
        console.error('jQuery não encontrado! Aguardando 1 segundo...');
        // Tente novamente após um pequeno atraso, caso jQuery seja carregado depois
        setTimeout(function() {
            if (typeof jQuery !== 'undefined') {
                CSExpress.Endereco.initHandlers();
            } else {
                console.error('jQuery não está disponível. A funcionalidade de busca de CEP não funcionará.');
            }
        }, 1000);
    }
});
