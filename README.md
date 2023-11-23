# Vérification de l'email Utilisateur

## Introduction

Nous avons vu dans la première partie du cours comment créer un formulaire d'inscription. Nous avons remarqué que Symfony nous propose de vérifier l'adresse électronique de la personne nouvellement inscrite. Voici comment bénéficier de cette fonctionnalité.

## Infrastructure d'envoi de mail

Différents contextes peuvent vous amener à avoir à configurer une fonctionnalité d'envoi de mail :
1. Vous n'avez pas encore de serveur accessible et/ou vous êtes en phase de développement,
2. Vous louez un hébergement chez un fournisseur d'accès (style OVH)
3. Vous voulez utiliser un service d'envoi de mail (comme MailChimp ou Gmail)

Nous examinons ici la première option, qui nécessite l'installation d'un serveur SMTP de test, comme **MailHog**.

### MailHog
MailHog est un outil de test d'envoi de mail qui se compose de :
- un serveur SMTP local
- une interface graphique pour lire les mails envoyés.

Optionnellement MailHog peut transférer les courriers vers un serveur SMTP opérationnel.

#### Installer MailHog

- Windows : https://github.com/mailhog/MailHog/releases/download/v1.0.0/MailHog_windows_amd64.exe

- macOS
```bash
brew update && brew install mailhog
```

- Linux
```bash
# Installer Go si ce n'est pas déjà fait
sudo apt-get -y install golang-go
```

#### Configuration du DSN
Une fois MailHog installé, il faut encore configurer l'application pour qu'elle trouve le serveur SMTP qui sera utilisé par le composant `Mailer`.
```yaml
# .env
MAILER_DSN="smtp://localhost:1025?auth_mode=login"
```
Vous pouvez ensuite consulter l'interface graphique dans votre navigateur.
Vous y trouverez les mails reçus, et différents outils vous permettant de challenger votre configuration.
```
http://0.0.0.0:8025/# ou http://localhost:8025
```

## Formulaire d'inscription et vérification du mail

### Bibliothèques tierces nécessaires
Pour nous faciliter la tâche de vérification, nous pouvons installer le « bundle » suivant :
```bash
composer require symfonycasts/verify-email-bundle
```
Il nous permet de générer et vérifier les liens cliquables qui seront envoyés par mail. Ces liens contiendront un token unique, comme nous l'avons déjà vu pour les formulaires. Ceci nous assure qu'un mail frauduleux ne pourra pas être accepté par l'application.

### Variante du formulaire d'inscription
:bulb: Rappel de la commande de création du formulaire
```
php bin/console make:registration-form
```
Contrairement à la version de la semaine 1, nous désirons maintenant _activer_ la vérification par mail.

#### Les questions posées
```text
Do you want to send an email to verify the user's email address after registration? (yes/no) [yes]:
```

Oui, c'est ce que l'on souhaite faire :)


```text
Would you like to include the user id in the verification link to allow anonymous email verification? (yes/no) [no]:
--> Est ce qu'il faut permettre la vérification de l'email sans être connecté ?
```

Pour comprendre cette question, il faut savoir qu'il y a deux usages pour la vérification des mails :


1. **Usage non connecté**
    * je m'inscris, je reçois un email de vérification.
    * je clique sur l'email, mon compte est validé.

2. **Usage connecté**
    * je m'inscris, je reçois un email de vérification.
    * je me connecte (sans avoir validé mon email). J'ai des droits limités ; par exemple, sur un forum, on imagine que j'ai des droits de lecture, mais pas de création de contenus.
    * maintenant que je suis connecté, je peux valider mon email en cliquant sur le lien de validation reçu. Mes droits d'écriture sont approuvés.

Le cas numéro 1 est le cas classique, nous allons donc répondre **yes** car nous voulons pouvoir valider notre email sans être connecté. Il faut donc que mon identifiant soit intégré au lien de validation.


```text
Do you want to automatically authenticate the user after registration? (yes/no)
--> Faut-il connecter immédiatement l'utilisateur après l'inscription ?
```

Comme il faut que l'on valide notre email avant de pouvoir se connecter, nous allons répondre **no** à cette question.

> **N.B.** :nerd_face: Depuis la version 5.3 de Symfony, cette fonctionnalité n'est de toute facon plus disponible via ce bundle.
> Si vous répondez “oui”, un message d'alerte vous indiquera que ce n'est pas possible et l'utilisateur ne sera pas connecté automatiquement.

##### Cas particulier de Messenger
:warning: `Messenger` est le composant de messagerie de Symfony (à ne pas confondre avec un service d'envoi de mail). Ce service, qui a beaucoup de cas d'utilisation variés, notamment de notification, utilise une file d'attente dans laquelle il stocke les messages à envoyer. Il se peut, surtout dans les versions récentes, que `Messenger` soit activé par défaut. Dans ce cas — regardez dans votre base de données — les messages ne sont pas envoyés immédiatement et vous ne recevrez donc pas vos mails. Ceci n'est pas un bug de votre application.

Pour désactiver la mise en file d'attente, vous pouvez commenter la ligne 19 du fichier de configuration de messenger.
Vous recevrez alors vos mails immédiatement.


```yaml
# Config/packages/messenger.yaml
routing:
            #Symfony\Component\Mailer\Messenger\SendEmailMessage: async
            Symfony\Component\Notifier\Message\ChatMessage: async
            Symfony\Component\Notifier\Message\SmsMessage: async
```


### :ninja: Allez plus loin, gestion des mails en production

#### Via un hébergeur
Si vous avez votre propre hébergement, vous pouvez très facilement renvoyer vos mails vers le serveur SMTP de celui-ci. Vous trouverez très facilement sur le web des tutoriels pour configurer l'accès. En voici un, par exemple, pour OVH :
-  [Utiliser Mailer de Symfony 5.4 avec OVH](https://www2.itroom.fr/composant-mailer-symfony-5-4-avec-ovh/)

Globalement, il vous suffira de configurer le DSN du service avec les indications qui vous sont fournies par l'hébergeur :
```ini
# .env
# L'exemple pour OVH donné dans l'article
# `ssl0.ovh.net` est l'adresse d'un serveur SMTP chez OVH, et `587` le port utilisé
MAILER_DSN=smtp://symfony@domaine.fr:MonMotDePasse2019@ssl0.ovh.net:587
```

> **N.B.** Avec cette méthode, vous exposez en clair vos identifiants de connexion dans votre fichier `.env` ! Dans ce cas, vous devrez impérativement chiffrer ces données avec le composant `Secrets` de Symfony.
> La marche à suivre est expliquée dans la documentation : https://symfony.com/doc/current/configuration/secrets.html

#### Via un service de mailing
Vous pouvez enfin vouloir utiliser des services comme Mailjet, Mailchimp, voire Gmail. Dans ce cas, il vous faudra installer les pilotes correspondant pour Symfony. La démarche est expliquée dans la [documentation de Symfony](https://symfony.com/doc/current/mailer.html#using-a-3rd-party-transport).

>**N.B.** Vous rencontrerez les mêmes problèmes de sécurité que précédemment, naturellement
>

# ユーザーのメールアドレス検証

## はじめに

コースの第1部で、登録フォームを作成する方法を見ました。Symfonyは新しく登録されたユーザーの電子メールアドレスを確認する機能を提供していることに気付きました。この機能を活用する方法を以下に示します。

## メール送信インフラ

異なる状況でメール送信機能を設定する必要があるかもしれません：
1. サーバーにアクセスできないか、または開発段階にある
2. プロバイダー（例：OVHのような）からホスティングを借りている
3. MailChimpやGmailのようなメール送信サービスを使用したい

ここでは最初のオプションを見ていきます。これには**MailHog**のようなテスト用SMTPサーバーのインストールが必要です。

### MailHog
MailHogはメール送信のテストツールで、以下から構成されています：
- ローカルSMTPサーバー
- 送信されたメールを読むためのグラフィカルなインターフェース

オプションで、MailHogはメールを実際のSMTPサーバーに転送することもできます。

#### MailHogのインストール

- Windows: [MailHog_windows_amd64.exe](https://github.com/mailhog/MailHog/releases/download/v1.0.0/MailHog_windows_amd64.exe)

- macOS
```bash
brew update && brew install mailhog
```

- Linux
```bash
# Goがまだインストールされていない場合は、まずインストールする
sudo apt-get -y install golang-go
```

#### DSNの設定
MailHogをインストールしたら、アプリケーションが`Mailer`コンポーネントで使用するSMTPサーバーを見つけるようにアプリケーションを構成する必要があります。
```yaml
# .env
MAILER_DSN="smtp://localhost:1025?auth_mode=login"
```
その後、ブラウザで以下のインターフェースにアクセスできます。
受信したメールや設定のチェックなどが行えます。
```
http://0.0.0.0:8025/# or http://localhost:8025
```

## 登録フォームとメール検証

### 必要な外部ライブラリ

検証を簡単にするために、次の「バンドル」をインストールできます。
```bash
composer require symfonycasts/verify-email-bundle
```
これにより、メールで送信されるクリック可能なリンクを生成および検証できるようになります。これらのリンクには、フォームで既に見たような一意のトークンが含まれており、これにより不正なメールがアプリケーションに受け入れられないことが保証されます。

### 登録フォームの変種
:bulb: フォームの作成コマンドのリマインダー
```
php bin/console make:registration-form
```
第1週のバージョンとは異なり、今回はメール検証を「有効にする」ことを望んでいます。

#### 質問される内容
```text
Do you want to send an email to verify the user's email address after registration? (yes/no) [yes]:
```

はい、これを行いたいです :)

```text
Would you like to include the user id in the verification link to allow anonymous email verification? (yes/no) [no]:
--> ログインせずにメールの検証を許可するためにユーザーIDを確認リンクに含めますか？
```

この質問を理解するには、メールの検証には2つの使い方があることを知っておく必要があります。

1. **未接続の使用**
    * 登録し、検証メールを受け取る
    * メールをクリックすると、アカウントが有効になります。

2. **接続済みの使用**
    * 登録し、検証メールを受け取る
    * 接続する（メールを有効にしていないまま）。制限された権限があります。たとえば、フォーラムの場合、読み取り権限はあるがコンテンツの作成権限はありません。
    * 今接続しているので、受け取った検証リンクをクリックしてメールを有効にできます。書き込み権限が承認されます。

ケース1は通常のケースですので、私たちは **yes** と答えます。つまり、接続せずにメールを検証できるように、検証リンクに私のIDを組み込む必要があります。

```text
Do you want to automatically authenticate the user after registration? (yes/no)
--> 登録後にユーザーを自動的に認証しますか？
```

メールを検証する前にメールアドレスを有効にする必要があるため、この質問には **no** と答えます。

> **注意**: :nerd_face: Symfonyのバージョン5.3以降では、このバンドルを介した方法でこの機能は利用できなくなりました。もし「はい」を選択すると、これは不可能である旨の警告メッセージが表示され、ユーザーは自動的に接続されません。

##### Messengerの特殊事例
:warning: `Messenger`はSymfonyのメッセージングコンポーネントで、メール送信サービスとは異なります。このサービスはさまざまな用途で使用され、通知などにも活用されます。特に最近のバージョンでは、`Messenger`がデフォルトで有効になっている可能性があります。この場合、データベース内でメッセージが直ちに送信されないため、メールを受信できません。これはアプリケーションのバグではありません。

キューイングを無効にするには、`messenger.yaml`ファイルの19行目をコメントアウトします。
その後、メールを即座に受信できるようになります。

```yaml
# Config/packages/messenger.yaml
routing:
            #Symfony\Component\Mailer\Messenger\SendEmailMessage: async
            Symfony\Component\Notifier\Message\ChatMessage: async
            Symfony\Component\Notifier\Message\SmsMessage: async
```

### :ninja: さらに進んで、本番環境でのメール管理

#### ホスティングサービスを介して
独自のホスティングがある場合は、メールをホスティングのSMTPサーバーにリダイレクトすることができます。設定手順はウェブ上で簡単に見つかります。たとえば、OVHの場合のチュートリアルは以下の通りです：
- [Symfony 5.4のMailerをOVHで使用する](https://www2.itroom.fr/composant-mailer-symfony-5-4-avec-ovh/)

基本的に、ホスティングプロバイダーから提供された指示に従ってサービスのDSNを設定するだけです。
```ini
# .env
# 記事で提供されているOVHの例
# `ssl0.ovh.net`はOVHのSMTPサーバーアドレスで、`587`は使用されるポートです
MAILER_DSN=smtp://symfony@domaine.fr:MonMotDePasse2019@ssl0.ovh.net:587
```

> **注意**: この方法では、`.env`ファイルに接続情報が平文で公開されます！この場合、Symfonyの`Secrets`コンポーネントを使用してこれらのデータを必ず暗号化する必要があります。手順はドキュメントに説明されています：[Secrets](https://symfony.com/doc/current/configuration/secrets.html)

#### メーリングサービスを介して
最後に、Mailjet、Mailchimp、またはGmailなどのサービスを使用したい場合、Symfony用の対応するトランスポートをインストールする必要があります。手順はSymfonyの[ドキュメント](https://symfony.com/doc/current/mailer.html#using-a-3rd-party-transport)に説明されています。

> **注意**: 自然にセキュリティの問題が発生する可能性があります