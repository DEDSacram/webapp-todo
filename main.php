<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./styles/main.css">
  <link rel="stylesheet" href="./styles/modal.css">
  <title>Vita vas Scheduwise</title>
</head>
<body>
  <div id="navbar">
    <!-- logopart -->
    <div>
      <img id="logo" alt="mainlogo" src="./img/logo.svg">
    </div>
    <div id="left">
      <ul>
        <li>
          <a id="brandname" href="#">ScheduWise</a>
        </li>
        <li>
          <a href="#">Co nabizime?</a>
        </li>
      </ul>
    </div>
    <span></span>
    <div id="right">
      <a href="#" id="showDialog">Prihlaseni</a>
    </div>
  </div>
  <div id="mainvideo">
    <video id="background-video" playsinline autoplay muted loop poster="./img/harold.png">
      <source src="./img/test.mp4" type="video/webm">
    </video>
  </div>

<div id="loginDialog" class="modal">
  <form class="modal-content animate">
    <div class="container">
      <label for="email"><b>Email</b></label>
      <input type="text" placeholder="Zadejte Email" name="email" required>

      <label for="psw"><b>Heslo</b></label>
      <input type="password" placeholder="Zadejte heslo"  name="password" required>
        
      <button type="submit" class="modal-btn-full">Přihlásit se</button>
      <label>
        <input type="checkbox" checked="checked" name="remember"> Zapamatovat si mě (30 dní)
      </label>
    </div>
    <div class="container-modal-bottom">
    <div class="container-column">
      <button type="button" class="cancelbtn">Zrušit</button>
    </div>
      <div class="container-column">
      <span class="psw"> <a href="#"  id="registerBtn">Nemáte účet?</a></span>
      <span class="psw"><a href="#">Zapomněli jste heslo?</a></span>
      </div>
    </div>
  </form>
</div>


<div id="registerDialog" class="modal">
  <form class="modal-content animate">
    <div class="container">
      <label for="email"><b>Email</b></label>
      <input type="text" placeholder="Zadejte Email" name="email" required>
      <br/>
      <span id="emailError" class="reminder-warning"></span>
      <br/>
      <label for="psw"><b>Heslo</b></label>
      <input type="password" placeholder="Zadejte heslo" name="password" required>
      <br/>
      <span id="passwordError" class="reminder-warning"></span>
      <br/>
      <label for="confirm_psw"><b>Potvrďte heslo</b></label>
      <input type="password" placeholder="Zadejte heslo znovu" name="confirmPassword" required>
      <br/>
      <span id="confirmPasswordError" class="reminder-warning"></span>
      <br/>
      <button type="submit" class="modal-btn-full">Registrovat se</button>
    </div>
    <div class="container-modal-bottom">
      <div class="container-column">
        <button type="button" class="cancelbtn">Zrušit</button>
      </div>
      <div class="container-column">
        <span class="psw"> <a href="#"  id="registerToLogin">Máte již účet?</a></span>
        <span class="psw"><a href="#">Zapomněli jste heslo?</a></span>
      </div>
    </div>
  </form>
</div>





  <section class="experience_section layout_padding">
    <div class="container">
      <div id="experience">
        <div id="img-box">
          <img src="img/test.png" alt="">
        </div>
        <span></span>
        <div class="detail-box-top">
          <h2> Vazite si sveho casu? </h2>
          <p> Proc nepřejít na vyšší úroveň organizace a plánování s naší revoluční platformou pro správu úkolů. Scheduwise vám přináší jednoduché a intuitivní prostředí, ve kterém můžete snadno plánovat, sledovat a dokončovat své úkoly. Rozloučte se s chaosem a vítejte v době účinného řízení času! </p>
        </div>
      </div>
    </div>
  </section>
  <section class="category_section layout_padding">
    <div class="container">
      <h2> Proc si vybrat nas </h2>
      <ul id="reasons">
        <li>
          <b>Uvolnění efektivity:</b> Naše aplikace na seznamy úkolů je navržena tak, aby optimalizovala vaše úkoly a zvýšila vaši produktivitu, zajistila, že zvládnete více v krátkém čase.
        </li>
        <li>
          <b>Přístup odkudkoliv a kdykoliv:</b> Přistupujte ke svému seznamu úkolů z telefonu, tabletu nebo počítače. Buďte v obraze ohledně svých úkolů, ať jste doma, v kanceláři nebo na cestách.
        </li>
        <li>
          <b>Snadná spolupráce:</b> Sdílejte a spolupracujte na úkolech s kolegy, přáteli nebo členy rodiny. Naše aplikace podporuje plynulou spolupráci a koordinaci.
        </li>
        <li>
          <b>Přizpůsobte si podle svého stylu:</b> Přizpůsobte si svůj seznam úkolů podle svých jedinečných potřeb. Od kódovaných barev po personalizované štítky – dejte svému seznamu úkolů svůj osobní nádech.
        </li>
        <li>
          <b>Bezpečnost dat:</b> Spějte klidně spát s vědomím, že vaše úkoly a osobní informace jsou v bezpečí. My kladejme důraz na ochranu dat, abyste měli klid na duši.
        </li>
      </ul>
    </div>
  </section>
  <section class="layout_padding_bottom">
    <div class="container">
      <div class="row">
        <div class="img-box">
          <img src="images/about-img.jpg" alt="">
        </div>
      </div>
      <h2> Kdo jsme? </h2>
      <div class="detail-box">
        <p> Jsme vas spolehlivý partner v oblasti produktivity. S našimi inovativními a uživatelsky přívětivými řešeními vam přinášíme jednoduchost ve zvládání úkolů. Připojte se k nám a zažijte nový standard v oblasti efektivity. Naše cíle jsou jasne, chceme podporovat váš úspěch. </p>
      </div>
    </div>
  </section>
  <section class="freelance_section ">
    <h2> Nasi partneri </h2>
    <div id="rotation">
      <div class="moving-image"></div>
      <div class="moving-image"></div>
      <div class="moving-image"></div>
      <div class="moving-image"></div>
    </div>
  </section>
  <section class="client_section layout_padding">
    <div class="container">
      <div class="row">
        <div>
          <h2> Nazory nasich uzivatelu </h2>
        </div>
        <div class="detail-box">
          <p> "Tato platforma změnila můj přístup k úkolům. Centrální přehled, intuitivní plánovač a včasné upozornění mi pomáhají udržet si pořádek a být stále na správném místě. Vřele doporučuji každému, kdo chce zvýšit produktivitu!" </p>
          <img src="./img/david.jpeg" alt="photodavid">
          <p> David z Nimbus Dynamics </p>
        </div>
        <div class="detail-box">
          <p> "Scheduwise je mým spolehlivým společníkem! Tato platforma nejen transformuje můj přístup k úkolům, ale také mi umožňuje snadno organizovat a plánovat svůj den. S přehledným rozhraním a efektivním plánovačem jsem vždy na vrcholu svých úkolů. Scheduwise není jen nástrojem, je to klíč k mé produktivitě!" </p>
          <img src="./img/martina.jpeg" alt="photomartina">
          <p> Martina z Vortex Visionu </p>
        </div>
        <div class="detail-box">
          <p> "Scheduwise mě nadchl! Tato aplikace nejen zlepšila mé plánování, ale také mi umožnila efektivněji spolupracovat na projektech. S centrálním přehledem a výborným upozorňováním jsem vždy krok před svými úkoly. Scheduwise není jen praktickým nástrojem, ale skutečným pomocníkem v mém každodenním životě!" </p>
          <img src="./img/harold.png" alt="photoharold">
          <p> Harold z EchoStreamu </p>
        </div>
      </div>
    </div>
  </section>
  <section class="info_section ">
    <div class="info-container">
      <div class="img-box">
        <img src="img/phone-call-icon.png" alt="">
      </div>
      <div class="row">
        <div class="detail-box">
          <h2> +420 776 898 649 </h2>
          <h4> Po - Pá: 7:00 - 17:00 </h4>
        </div>
      </div>
    </div>
    <div id="partners" class="layout_padding">
      <img src="./img/logo1.svg" alt="logo1">
      <img src="./img/logo2.svg" alt="logo2">
      <img src="./img/logo3.svg" alt="logo3">
      <img src="./img/logo4.svg" alt="logo4">
      <img src="./img/logo5.svg" alt="logo5">
      <img src="./img/logo6.svg" alt="logo6">
      <img src="./img/logo7.svg" alt="logo7">
    </div>
  </section>
  <footer class="container-fluid footer_section ">
    <div class="container">
      <p> © 2023 Ondrej Skutil </p>
    </div>
  </footer>
  <script src="modalscript.js"></script>
  <script src="rotation.js"></script>
</body>
</html>