
# CustomWooCheckout

CustomWooCheckout é uma classe PHP para adicionar campos customizados ao checkout do WooCommerce, permitindo diferenciar Pessoa Física (PF) e Pessoa Jurídica (PJ) com campos específicos, além de recursos para preenchimento automático e validação simples.

---

## Funcionalidades

- Adiciona campo "Tipo de Pessoa" (PF ou PJ).
- Campos dinâmicos visíveis conforme seleção (CPF, CNPJ, IE, Data de nascimento).
- Preenchimento automático de endereço via CEP (ViaCEP).
- Opção para copiar dados para o formulário de cobrança.
- Tradução customizada do título "Billing details" para "Detalhes do pedido".
- Scripts JS para controle dinâmico dos campos.

---

## Instalação

### Opção 1: Clonar o repositório no seu projeto (recomendado)

1. Clone o repositório na pasta desejada do seu tema ou plugin:

```bash
cd /caminho/para/seu/projeto/wp-content/themes/seu-tema/app
git clone https://github.com/manobahiadev/customwoocheckout.git
```

2. No arquivo `functions.php` do seu tema, insira o seguinte para carregar a classe:

```php
require_once get_template_directory() . '/app/customwoocheckout/src/CustomCheckoutFields.php';
\App\CustomWoocheckout\CustomCheckoutFields::init();
```

### Opção 2: Copiar o código manualmente

1. Copie o arquivo `CustomCheckoutFields.php` da pasta `src` do repositório para uma pasta do seu projeto, por exemplo:

```
wp-content/themes/seu-tema/app/custom-woocheckout/src/CustomCheckoutFields.php
```

2. No `functions.php`, inclua e inicialize a classe assim:

```php
require_once get_template_directory() . '/app/customwoocheckout/src/CustomCheckoutFields.php';
\App\CustomWoocheckout\CustomCheckoutFields::init();
```

---

## Como usar

- Após incluir a classe e chamar o método `init()`, os campos customizados estarão disponíveis no checkout do WooCommerce automaticamente.
- O JavaScript embutido cuida da exibição dinâmica dos campos conforme a seleção do tipo de pessoa.
- O preenchimento pelo CEP usa o serviço público ViaCEP.
- Os campos são salvos como metadados do pedido para futura consulta.

---

## Estrutura do repositório

```
customwoocheckout/
├── src/
│   └── CustomCheckoutFields.php    # Classe principal PHP
├── README.md                      # Este arquivo
```

---
Copyright (c) 2025 [ManoBahia.Dev]

---

## Considerações finais

- Este projeto **não é um plugin WordPress** — é uma biblioteca PHP para facilitar a reutilização e manutenção dos campos customizados do checkout.
- Recomenda-se usar Composer ou outro gerenciador para projetos mais complexos.
- Para dúvidas, abra issues no repositório GitHub.

---

**Desenvolvido por manobahiadev**  
https://github.com/manobahiadev/customwoocheckout

---

## Licença

MIT License


