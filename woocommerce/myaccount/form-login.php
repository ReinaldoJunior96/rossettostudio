<?php
defined('ABSPATH') || exit;

/**
 * Linter-friendly: alguns editores não “veem” as funções do Woo fora do runtime.
 */
$registration_enabled = function_exists('wc_registration_enabled')
   ? wc_registration_enabled()
   : ('yes' === get_option('woocommerce_enable_myaccount_registration'));
?>

<div class="flex justify-center">
   <div class="grid grid-cols-12 py-10 margin-site">

      <!-- LOGIN (esquerda) -->
      <div class="relative col-span-12 lg:col-span-6">
         <div class="pointer-events-none absolute -inset-2 rounded-3xl bg-gradient-to-br from-purple-500/15 via-fuchsia-400/10 to-cyan-400/10 blur-xl"></div>

         <div class="relative rounded-3xl bg-white/80 backdrop-blur-xl ring-1 ring-black/5 shadow-[0_20px_60px_-20px_rgba(17,24,39,0.25)] p-6 md:p-8 w-full max-w-lg">
            <div class="mb-6 flex items-center gap-3">
               <div class="grid h-10 w-10 place-items-center rounded-2xl bg-purple-600 text-white shadow-md">
                  <i class="fa-solid fa-right-to-bracket"></i>
               </div>
               <div>
                  <h2 class="text-xl font-extrabold text-gray-900 leading-tight">Entrar</h2>
                  <p class="text-sm text-gray-500">Bem-vindo de volta ao Rossetto Studio</p>
               </div>
            </div>

            <?php if (function_exists('wc_print_notices')) wc_print_notices(); ?>

            <form class="space-y-5" method="post">
               <?php do_action('woocommerce_login_form_start'); ?>

               <div>
                  <label for="username" class="block text-sm font-semibold text-gray-800 mb-1.5">Usuário ou e-mail</label>
                  <div class="relative">
                     <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fa-regular fa-envelope"></i>
                     </span>
                     <input
                        type="text"
                        id="username"
                        name="username"
                        autocomplete="username"
                        class="w-full rounded-2xl border border-gray-200 bg-white/90 px-10 py-2.5 text-[15px] text-gray-900 placeholder:text-gray-400 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                        placeholder="seu@email.com" />
                  </div>
               </div>

               <div>
                  <label for="password" class="block text-sm font-semibold text-gray-800 mb-1.5">Senha</label>
                  <div class="relative">
                     <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fa-solid fa-lock"></i>
                     </span>
                     <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        class="w-full rounded-2xl border border-gray-200 bg-white/90 px-10 py-2.5 text-[15px] text-gray-900 placeholder:text-gray-400 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                        placeholder="••••••••" />
                     <button type="button" id="toggle-pass"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        aria-label="Mostrar senha">
                        <i class="fa-regular fa-eye"></i>
                     </button>
                  </div>
               </div>

               <?php do_action('woocommerce_login_form'); ?>

               <div class="flex items-center justify-between gap-4">
                  <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer select-none">
                     <input name="rememberme" value="forever" type="checkbox" class="peer sr-only">
                     <span class="h-5 w-9 rounded-full bg-gray-200 relative transition
                after:absolute after:top-0.5 after:left-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow
                peer-checked:bg-purple-600 peer-checked:after:translate-x-4 peer-focus:outline-none"></span>
                     Lembrar de mim
                  </label>

                  <a class="text-sm font-medium text-purple-700 hover:underline"
                     href="<?php echo esc_url(wp_lostpassword_url()); ?>">
                     Esqueceu a senha?
                  </a>
               </div>

               <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>

               <button type="submit" name="login" value="Entrar"
                  class="w-full rounded-2xl bg-purple-700 px-4 py-3 text-white font-semibold tracking-tight
            shadow-[0_10px_30px_-10px_rgba(109,40,217,.7)] hover:bg-purple-800 transition">
                  Entrar
               </button>

               <?php do_action('woocommerce_login_form_end'); ?>
            </form>
         </div>
      </div>

      <!-- REGISTRO (direita) -->
      <div class="col-span-12 lg:col-span-6 mt-8 lg:mt-0">
         <?php if ($registration_enabled) : ?>
            <div class="relative">
               <div class="pointer-events-none absolute -inset-2 rounded-3xl bg-gradient-to-br from-purple-500/15 via-fuchsia-400/10 to-cyan-400/10 blur-xl"></div>

               <div class="relative rounded-3xl bg-white/80 backdrop-blur-xl ring-1 ring-black/5 shadow-[0_20px_60px_-20px_rgba(17,24,39,0.25)] p-6 md:p-8">
                  <div class="mb-6 flex items-center gap-3">
                     <div class="grid h-10 w-10 place-items-center rounded-2xl bg-purple-600 text-white shadow-md">
                        <i class="fa-solid fa-user-plus"></i>
                     </div>
                     <div>
                        <h2 class="text-xl font-extrabold text-gray-900 leading-tight">Criar conta</h2>
                        <p class="text-sm text-gray-500">Cadastre-se para comprar e acompanhar pedidos</p>
                     </div>
                  </div>

                  <form method="post" class="space-y-5">
                     <?php do_action('woocommerce_register_form_start'); ?>

                     <div>
                        <label for="reg_email" class="block text-sm font-semibold text-gray-800 mb-1.5">E-mail *</label>
                        <div class="relative">
                           <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                              <i class="fa-regular fa-envelope"></i>
                           </span>
                           <input
                              type="email"
                              id="reg_email"
                              name="email"
                              required
                              autocomplete="email"
                              placeholder="seu@email.com"
                              class="w-full rounded-2xl border border-gray-200 bg-white/90 px-10 py-2.5 text-[15px] text-gray-900 placeholder:text-gray-400 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200" />
                        </div>
                     </div>

                     <!-- Senha SEMPRE exibida (definição no próprio formulário) -->
                     <div>
                        <label for="reg_password" class="block text-sm font-semibold text-gray-800 mb-1.5">Senha *</label>
                        <div class="relative">
                           <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                              <i class="fa-solid fa-lock"></i>
                           </span>
                           <input
                              type="password"
                              id="reg_password"
                              name="password"
                              required
                              autocomplete="new-password"
                              placeholder="Mínimo de 6 caracteres"
                              class="w-full rounded-2xl border border-gray-200 bg-white/90 px-10 py-2.5 text-[15px] text-gray-900 placeholder:text-gray-400 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200" />
                           <button type="button" id="toggle-pass-reg"
                              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                              aria-label="Mostrar senha">
                              <i class="fa-regular fa-eye"></i>
                           </button>
                        </div>

                        <div class="mt-2 h-1.5 w-full rounded-full bg-gray-200 overflow-hidden">
                           <div id="pwd-bar" class="h-full w-0 transition-all"></div>
                        </div>
                        <p id="pwd-hint" class="mt-1 text-xs text-gray-500">Use letras, números e pelo menos 6 caracteres.</p>
                     </div>

                     <?php
                     // Seus campos extras + "Confirmar senha" vêm do hook em functions.php
                     do_action('woocommerce_register_form');
                     ?>

                     <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>

                     <button type="submit" name="register" value="Registrar"
                        class="w-full rounded-2xl bg-purple-700 px-4 py-3 text-white font-semibold tracking-tight
                shadow-[0_10px_30px_-10px_rgba(109,40,217,.7)] hover:bg-purple-800 transition">
                        Registrar
                     </button>

                     <?php do_action('woocommerce_register_form_end'); ?>
                  </form>
               </div>
            </div>

            <script>
               (function() {
                  const pass = document.getElementById('reg_password');
                  const btn = document.getElementById('toggle-pass-reg');
                  const bar = document.getElementById('pwd-bar');

                  if (btn && pass) {
                     btn.addEventListener('click', function() {
                        const isPwd = pass.type === 'password';
                        pass.type = isPwd ? 'text' : 'password';
                        this.setAttribute('aria-label', isPwd ? 'Ocultar senha' : 'Mostrar senha');
                        const icon = this.querySelector('i');
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                     });

                     pass.addEventListener('input', function() {
                        const v = this.value || '';
                        let score = 0;
                        if (v.length >= 6) score++;
                        if (/[A-Z]/.test(v)) score++;
                        if (/[0-9]/.test(v)) score++;
                        if (/[^A-Za-z0-9]/.test(v)) score++;
                        const pct = [0, 25, 50, 75, 100][score];
                        bar.style.width = pct + '%';
                        bar.style.background = pct < 50 ? '#f87171' : (pct < 75 ? '#f59e0b' : '#22c55e');
                     });
                  }
               })();
            </script>
         <?php endif; ?>
      </div>

   </div>
</div>

<script>
   document.addEventListener('click', function(e) {
      const btn = e.target.closest('#toggle-pass');
      if (!btn) return;
      const input = document.getElementById('password');
      const icon = btn.querySelector('i');
      const isPwd = input.type === 'password';
      input.type = isPwd ? 'text' : 'password';
      btn.setAttribute('aria-label', isPwd ? 'Ocultar senha' : 'Mostrar senha');
      icon.classList.toggle('fa-eye');
      icon.classList.toggle('fa-eye-slash');
   });
</script>