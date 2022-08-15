# Testello
## Como executar a aplicação
### Requisitos
 - PHP 8
 - MySql
 - Composer

### Configurando
Renomear `.env.sample` para `.env`
É necessário mudar as variaveis abaixo de acordo com o seu ambiente
 - DB_HOST
 - DB_PORT
 - DB_DATABASE
 - DB_USERNAME
 - DB_PASSWORD

É necessário também executar os seguintes comandos
```shell
php artisan migrate
php artisan db:seed
```

 ### Executando
 Para executar a aplicação é necessário 2 componentes rodando:

 O Job System:
 ```
 php artisan queue:work --timeout=600
 ```
 
 O servidor:
 ```
 php artisan serve
 ```
 A aplicaçao pode ser acessada por `http://localhost:8000`

## Detalhes da implementaçao
A aplicaçao tem apenas duas telas, uma para o acompanhamentos do upload das tabelas de preço e outra para o próprio upload.

Para solucionar a problemática da tabela de preços poder ter um tamanho elevado e ser impossível processar um request de forma adequada, foi decidido a criaçao de um job feito em background, reportando o status do processamento na tela principal.

Não ficou claro o funcionamento da tabela de preços, sendo assim, assumi que o conjunto de campos que representam a faixa de cep inicial e final junto com a faixa de peso inicial e final não se repetem, e por isso eu não permito a duplicaço desses 4 campos. Caso esse nao for o cenário real, minha abordagem seria outra, provavelmente removendo e reinserindo os dados do cliente em questao.

Entendi também que cada tabela de preços está vinculada a um unico cliente, por isso uso o id do mesmo para separaçao dos dados da tabela de preços (no banco de dados). Se esse nao for o caso, deveriamos seguir com outra abordagem, provavelmente uma tela de seleçao manual ou algum tipo de agrupamento.

O foco principal foi na soluçao do processamento do csv, e foi aplicado menor esforço na interface.
## Detalhes do teste

```
Testello

Somos uma transportadora e prestamos serviço para N clientes. Cada um possui sua tabela de frete com reajuste periódico.

Portanto, quando chega a época do reajuste são realizados manualmente a alteração de cada um dos clientes gerando custos para a empresa por alocação de horas de trabalho.

Precisamos criar uma solução que permita subir um CSV com a respectiva tabela de frete de cada um dos Clientes (1 ou +) de maneira eficiente e que suporte uma grande quantidade de registros (Essas tabelas podem chegar a ter 300mil linhas).

Como podemos resolver esse problema? De que maneira conseguimos fazer o upload de 1 ou + CSV's sem que o HTTP dê timeout?

Requisitos negócio:

- Criar estrutura banco de dados:    
- Importar um arquivo CSV de tabela de frete de Cliente(s);
- Salvar em Banco de dados;

Requisitos Técnicos:

- controle de versionamento (GIT)
- PHP 7/8;
- Utilizar Composer para libs externas;
- Utilize o framework que se sentir confortável (ou não utilize);

O que se espera: 

- Utilização de PSR (https://www.php-fig.org/psr/ PSR-1 e PSR-12)
- Desenvolvimento da Lógica para leitura do CSV;
- Validação e cleanup dos dados;
- Estruturação da tabela;
- Salvar dados DB;
- Escrever um README com passo a passo para reproduzir o teste;

Diferenciais:

- Clean code;
- Docker;
- TDD;
- Faker/Mockery;

Como entregar:
Responda o email do teste com o link do repositório;
```

