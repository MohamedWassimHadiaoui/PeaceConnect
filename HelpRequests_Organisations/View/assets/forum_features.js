(() => {
  function decodeHtmlEntities(html) {
    const txt = document.createElement('textarea');
    txt.innerHTML = html;
    return txt.value;
  }

  let currentButton = null;
  let isSpeaking = false;

  function resetButton(btn) {
    if (!btn) return;
    btn.classList.remove('speaking');
    const original = btn.getAttribute('data-original-text');
    if (original) btn.textContent = original;
    btn.title = 'Read aloud';
  }

  function setButtonSpeaking(btn) {
    if (!btn) return;
    if (!btn.getAttribute('data-original-text')) {
      btn.setAttribute('data-original-text', btn.textContent);
    }
    btn.classList.add('speaking');
    btn.textContent = btn.getAttribute('data-original-text') === '🔊' ? '🔇' : 'Stop';
    btn.title = 'Stop reading';
  }

  function stopAll() {
    window.speechSynthesis.cancel();
    isSpeaking = false;
    if (currentButton) {
      resetButton(currentButton);
      currentButton = null;
    }
  }

  function speak(btn) {
    if (!('speechSynthesis' in window)) {
      alert('Text-to-speech not supported in your browser');
      return;
    }

    if (isSpeaking && currentButton === btn) {
      stopAll();
      return;
    }

    if (isSpeaking) {
      stopAll();
    }

    let title = decodeHtmlEntities(btn.getAttribute('data-titre') || btn.getAttribute('data-title') || '');
    let content = decodeHtmlEntities(btn.getAttribute('data-contenu') || btn.getAttribute('data-content') || '');
    
    title = title.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
    content = content.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
    
    const text = (title ? title + '. ' : '') + content;
    if (!text) return;

    const textToSpeak = text.length > 3000 ? text.substring(0, 3000) : text;

    const utterance = new SpeechSynthesisUtterance(textToSpeak);
    utterance.lang = 'en-US';
    utterance.rate = 0.9;
    utterance.pitch = 1;
    utterance.volume = 1;

    const voices = window.speechSynthesis.getVoices();
    const englishVoice = voices.find(v => v.lang.startsWith('en-')) || voices.find(v => v.lang.startsWith('en'));
    if (englishVoice) {
      utterance.voice = englishVoice;
    }

    utterance.onstart = () => {
      isSpeaking = true;
      currentButton = btn;
      setButtonSpeaking(btn);
    };

    utterance.onend = () => {
      isSpeaking = false;
      resetButton(btn);
      currentButton = null;
    };

    utterance.onerror = (e) => {
      if (e.error !== 'interrupted' && e.error !== 'canceled') {
        console.log('Speech error:', e.error);
      }
      isSpeaking = false;
      resetButton(btn);
      currentButton = null;
    };

    setButtonSpeaking(btn);
    currentButton = btn;
    isSpeaking = true;

    setTimeout(() => {
      window.speechSynthesis.speak(utterance);
      
      const interval = setInterval(() => {
        if (!isSpeaking) {
          clearInterval(interval);
          return;
        }
        if (window.speechSynthesis.paused) {
          window.speechSynthesis.resume();
        }
      }, 300);
      
      setTimeout(() => clearInterval(interval), 120000);
    }, 50);
  }

  function initSpeech() {
    window.speechSynthesis.getVoices();
    
    if (window.speechSynthesis.onvoiceschanged !== undefined) {
      window.speechSynthesis.onvoiceschanged = () => {
        window.speechSynthesis.getVoices();
      };
    }

    document.querySelectorAll('.speech-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        speak(btn);
      });
    });
  }

  const translationCache = {};

  function detectLanguage(text) {
    if (/[\u0600-\u06FF]/.test(text)) return 'ar';
    if (/[àâäéèêëïîôùûüÿçœæ]/i.test(text)) return 'fr';
    if (/\b(le|la|les|de|du|des|et|est|sont|pour|avec|dans|une|que|qui|vous|nous)\b/i.test(text)) return 'fr';
    return 'en';
  }

  async function translateText(text, targetLang, sourceLang) {
    const t = (text || '').trim();
    if (!t) return '';
    const src = sourceLang || detectLanguage(t);
    if (src === targetLang) return t;
    const key = t + '||' + src + '||' + targetLang;
    if (translationCache[key]) return translationCache[key];
    const url = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(t.substring(0,500))}&langpair=${src}|${targetLang}`;
    const res = await fetch(url);
    const data = await res.json();
    if (data?.responseStatus === 200 && data?.responseData?.translatedText) {
      translationCache[key] = data.responseData.translatedText;
      return data.responseData.translatedText;
    }
    throw new Error('Translation failed');
  }

  async function translatePost(postId, targetLang) {
    const titleEl = document.getElementById('post-title-' + postId);
    const contentEl = document.getElementById('post-content-' + postId);
    if (!titleEl || !contentEl) return;

    const originalTitle = decodeHtmlEntities(titleEl.getAttribute('data-original') || '');
    const originalSnippet = decodeHtmlEntities(contentEl.getAttribute('data-original') || '');
    const originalFull = decodeHtmlEntities(contentEl.getAttribute('data-original-full') || '');
    const srcLang = detectLanguage(originalTitle + ' ' + originalFull);

    if (targetLang === 'original') {
      titleEl.textContent = originalTitle;
      contentEl.textContent = originalSnippet;
      titleEl.querySelector('.translated-badge')?.remove();
      titleEl.classList.remove('rtl-text');
      contentEl.classList.remove('rtl-text');
      return;
    }

    titleEl.classList.add('translating');
    contentEl.classList.add('translating');

    try {
      const [tTitle, tBody] = await Promise.all([
        translateText(originalTitle, targetLang, srcLang),
        translateText(originalFull || originalSnippet, targetLang, srcLang)
      ]);

      titleEl.textContent = tTitle;
      contentEl.textContent = tBody.length > 220 ? tBody.slice(0, 220) + '...' : tBody;
      
      const oldBadge = titleEl.querySelector('.translated-badge');
      if (oldBadge) oldBadge.remove();
      const badge = document.createElement('span');
      badge.className = 'translated-badge';
      const flags = {ar:'🇸🇦', en:'🇬🇧', fr:'🇫🇷'};
      badge.textContent = flags[targetLang] || '';
      titleEl.appendChild(badge);

      if (targetLang === 'ar') {
        titleEl.classList.add('rtl-text');
        contentEl.classList.add('rtl-text');
      } else {
        titleEl.classList.remove('rtl-text');
        contentEl.classList.remove('rtl-text');
      }
    } catch (e) {
      titleEl.textContent = originalTitle;
      contentEl.textContent = originalSnippet;
    }

    titleEl.classList.remove('translating');
    contentEl.classList.remove('translating');
  }

  function initTranslate() {
    document.querySelectorAll('.translate-dropdown').forEach(dd => {
      const btn = dd.querySelector('.translate-btn');
      const menu = dd.querySelector('.translate-menu');
      const postId = dd.getAttribute('data-post-id');
      if (!btn || !menu || !postId) return;

      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        document.querySelectorAll('.translate-menu.show').forEach(m => {
          if (m !== menu) m.classList.remove('show');
        });
        menu.classList.toggle('show');
      });

      menu.querySelectorAll('button[data-lang]').forEach(opt => {
        opt.addEventListener('click', async (e) => {
          e.preventDefault();
          e.stopPropagation();
          menu.classList.remove('show');
          await translatePost(postId, opt.getAttribute('data-lang'));
        });
      });
    });

    document.addEventListener('click', () => {
      document.querySelectorAll('.translate-menu.show').forEach(m => m.classList.remove('show'));
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initSpeech();
    initTranslate();
  });
})();
