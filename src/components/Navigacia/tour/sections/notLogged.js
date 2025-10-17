// src/components/Navigacia/tour/sections/notLogged.js
// Nultý (nologged) flow – avatar + voľby. Po "Registrovať" zobrazíme JEDEN krok
// nad časťou formulára: iba zelené polia (Meno → Krajina).

export function steps() {
  return [
    {
      type: "between",
      title: "🙋‍♂️ Ahoj, som Andrej!",
      text: "Vitaj na Heligónkovej Akadémii 👋 Ak chceš pokračovať v prehliadke, prihlás sa alebo si vytvor nový účet.",
      options: [
        {
          label: "Registrovať",
          goto: "/registracia",
          // ⬇️ JEDEN krok: zvýrazníme len zelené polia (Meno → Krajina)
          steps: () => [
            {
              id: "reg-basics",
              title: "📝 Vyplň základné údaje",
              text: "Zadaj svoje meno, priezvisko, e-mail a heslo. Potom vyber krajinu – a si pripravený na hudobnú cestu.",
              side: "right",
              radius: 16,
              pad: { x: 20, y: 16 },

              // Preferuje wrapper (ak ho máš), inak zoberie 6 .row riadkov.
              // bindStepAt() urobí spoločný bbox všetkých nájdených selektorov.
              selectors: [
                // 1) Wrapper (ak si ho pridal podľa návodu)
                "#reg-block-basic",
                ".register-form .reg-block-basic",

                // 2) Priamy výber prvých 6 riadkov vo formulári (Meno → Krajina)
                ".register-form .row:nth-of-type(1)", // Meno
                ".register-form .row:nth-of-type(2)", // Priezvisko
                ".register-form .row:nth-of-type(3)", // Email
                ".register-form .row:nth-of-type(4)", // Heslo
                ".register-form .row:nth-of-type(5)", // Heslo znovu
                ".register-form .row:nth-of-type(6)", // Krajina (select)

                // 3) Fallbacky pre mierne iné štruktúry/ID (s/bez diakritiky)
                "section#Registrácia.computer form .row:nth-of-type(1)",
                "section#Registrácia.computer form .row:nth-of-type(2)",
                "section#Registrácia.computer form .row:nth-of-type(3)",
                "section#Registrácia.computer form .row:nth-of-type(4)",
                "section#Registrácia.computer form .row:nth-of-type(5)",
                "section#Registrácia.computer form .row:nth-of-type(6)",

                "section#Registracia.computer form .row:nth-of-type(1)",
                "section#Registracia.computer form .row:nth-of-type(2)",
                "section#Registracia.computer form .row:nth-of-type(3)",
                "section#Registracia.computer form .row:nth-of-type(4)",
                "section#Registracia.computer form .row:nth-of-type(5)",
                "section#Registracia.computer form .row:nth-of-type(6)",

                ".computer.registracia form .row:nth-of-type(1)",
                ".computer.registracia form .row:nth-of-type(2)",
                ".computer.registracia form .row:nth-of-type(3)",
                ".computer.registracia form .row:nth-of-type(4)",
                ".computer.registracia form .row:nth-of-type(5)",
                ".computer.registracia form .row:nth-of-type(6)",
              ],

              onBeforeEnter() {
                // Jemne posuň obraz tak, aby bol začiatok bloku v strede
                const first =
                  document.querySelector("#reg-block-basic") ||
                  document.querySelector(".register-form .reg-block-basic") ||
                  document.querySelector(
                    ".register-form .row:nth-of-type(1)"
                  ) ||
                  document.querySelector(
                    "section#Registrácia.computer form .row:nth-of-type(1)"
                  ) ||
                  document.querySelector(
                    "section#Registracia.computer form .row:nth-of-type(1)"
                  ) ||
                  document.querySelector(
                    ".computer.registracia form .row:nth-of-type(1)"
                  );

                first?.scrollIntoView({ block: "center", behavior: "smooth" });
              },
            },
          ],
        },

        {
          label: "Prihlásiť sa",
          goto: "/prihlasenie",
          steps: () => [
            {
              id: "login-basic",
              selectors: [
                "#login-block-basic",
                ".login-block-basic",
                "section[id*='Prihl'] form",
                "div[id*='Prihl'] form",
                ".computer.prihlasenie .formular",
                ".computer.prihlasenie form",
                "form[action*='logIn']",
                "form[action*='auth/logIn']",
                "#login",
                "form#login",
                ".login-form",
                "form[name*='login']",
              ],
              title: "🔑 Prihlás sa do účtu",
              text: "Zadaj svoj e-mail a heslo. Ak si ho nepamätáš, klikni na „Zabudnuté heslo“. Po prihlásení ťa čaká celý svet heligónky!",
              side: "right",
              pad: { x: 24, y: 20 },
              radius: 16,
              onBeforeEnter() {
                document
                  .querySelector(
                    "#login-block-basic,.login-block-basic,.computer.prihlasenie form,#login,.login-form"
                  )
                  ?.scrollIntoView({ block: "center", behavior: "smooth" });
              },
            },
          ],
        },
      ],
    },
  ];
}
