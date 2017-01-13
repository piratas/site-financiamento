# site-financiamento
Crowdfunding em docker/wordpress
Código fonte do site anapirata.partidopirata.org
===

Este repositório compreende o arquivo [docker-compose](https://docs.docker.com/compose/overview) e os arquivos de [plugins](https://wordpress.org/plugins/) e [temas](https://wordpress.org/themes/) do [wordpress](https://wordpress.org/) para o site <http://anapirata.partidopirata.org>.

Instruções
---

### Docker

Para usar o site localmente, é necessário primeiro instalar o [Docker](https://docs.docker.com).

#### Instalar docker-engine

As instruções para isto dependem do sistema operacional de quem for instalar. Leia o manual: <https://docs.docker.com/engine/installation/>

#### Instalar docker-compose

A instalação do `docker-compose` também depende do ambiente. Leia o manual: <https://docs.docker.com/compose/install/>

#### Configurar ambiente

Copie os arquivos `mariadb.env.exemplo` e `wordpress.env.exemplo` para `mariadb.env` e `wordpress.env`, respectivamente. Defina as senhas do banco de dados nestes arquivos.

O seguintes valores devem ser **iguais**:

Valor em `mariadb.env` | Valor em `wordpress.env`
--- | ---
`MYSQL_DATABASE` | `WORDPRESS_DB_NAME`
`MYSQL_USER` | `WORDPRESS_DB_USER`
`MYSQL_PASSWORD` | `WORDPRESS_DB_PASSWORD`

### Configurações

#### Corrigir permissões

Os diretórios `languages`, `plugins`, `themes` e `uploads` só funcionarão corretamente no wordpress caso pertençam ao usuário do servidor web.

Para garantir isto, em um sistema UNIX, use:

```bash
sudo chown -hR 33:33 languages plugins themes uploads
```

### Iniciar serviço

Caso tudo esteja devidamente instalado e configurado, o seguinte comando fará tudo acontecer magicamente (ou causar um problema difícil de recuperar caso as instruções não tenham sido lidas):

```
docker-compose up -d
```

O site deve estar acessível no navegador web no endereço `http://0.0.0.0:42082`.

Problemas
---

Ajuda é bem vinda.

### Wordpress

Atualmente ainda não consta neste repositório os arquivos do banco de dados, ou mesmo dados de importação e exportação do wordpress. Isto significa que as publicações no site do partido ainda estão indisponíveis em uma nova instalação. Isto é sabido e está registrado na [issue #2 do repositório do site principal](https://github.com/piratas/piratas-site/issues/2).

Como consequência, o site gerado parecerá igual ao do partido, contudo, diversas partes estarão faltando. Inclusive, os temas e plugins pertinentes não estarão ativados por padrão. Além disto, é necessário passar pela boçal "instalação de 5 minutos" do wordpress.

### E-mail

Atualmente as imagens utilizadas do wordpress não têm e-mail bem configurado. Isto é sabido e está para ser resolvido na [issue #1 do repositório do site da anapirata](https://github.com/piratas/anapirata-site/issues/1).

### Como ajudar

Tente falar com o GTI pelos métodos propostos no [site](https://gti.partidopirata.org), ou então envie pull requests no [github](https://github.com/piratas) ou mesmo no [notabug](https://notabug.org/piratas).
