/**
 * Language Translator System
 * Provides real-time translation using Google Translate API
 */

class LanguageTranslator {
    constructor() {
        this.currentLanguage = localStorage.getItem('preferred_language') || 'en';
        this.translationCache = new Map();
        this.originalTexts = new Map();
        this.isTranslating = false;
        this.initializeWidget();
    }

    // Translation with dictionary fallback and Google API
    async translateText(text, targetLanguage) {
        if (!text || text.trim() === '' || targetLanguage === 'en') {
            return text;
        }

        // Check cache first
        const cacheKey = `${text}_${targetLanguage}`;
        if (this.translationCache.has(cacheKey)) {
            return this.translationCache.get(cacheKey);
        }

        // Try dictionary translation first (for instant feedback)
        const dictionaryTranslation = this.getDictionaryTranslation(text.trim(), targetLanguage);
        if (dictionaryTranslation && dictionaryTranslation !== text) {
            this.translationCache.set(cacheKey, dictionaryTranslation);
            return dictionaryTranslation;
        }

        // Try Google Translate API as fallback
        try {
            const response = await fetch('/translate-text.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    text: text,
                    target: targetLanguage
                })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.translatedText) {
                    this.translationCache.set(cacheKey, data.translatedText);
                    return data.translatedText;
                }
            }
        } catch (error) {
            console.warn('Translation API error, using dictionary fallback:', error);
        }

        // Return dictionary translation or original
        return dictionaryTranslation || text;
    }

    // Dictionary translation as fallback
    getDictionaryTranslation(text, targetLanguage) {
        const translations = {
            so: { // Somali
                'Dashboard': 'Looska',
                'Products': 'Alaabada',
                'View Products': 'Eeg Alaabada',
                'Add Product': 'Ku dar Alaab',
                'Suppliers': 'Bixiyeyaasha',
                'View Suppliers': 'Eeg Bixiyeyaasha',
                'Add Supplier': 'Ku dar Bixiye',
                'Purchase Orders': 'Dalabka Iibsiga',
                'View Orders': 'Eeg Dalabka',
                'Create Order': 'Samee Dalab',
                'Users': 'Isticmaalayaasha',
                'View Users': 'Eeg Isticmaalayaasha',
                'Add User': 'Ku dar Isticmaale',
                'Reports': 'Warbixinnada',
                'Settings': 'Dejinta',
                'Total Products': 'Wadarta Alaabada',
                'Monthly Revenue': 'Dakhliga Bishii',
                'Low Stock Items': 'Alaabada Yar',
                'Stock-Out Items': 'Alaabada Dhamaaday',
                'Language': 'Luuqadda',
                'Save': 'Kaydi',
                'Cancel': 'Jooji',
                'Delete': 'Tirtir',
                'Edit': 'Wax ka beddel',
                'View': 'Eeg',
                'Search': 'Raadi',
                'Login': 'Gal',
                'Logout': 'Ka bax'
            },
            vi: { // Vietnamese
                'Dashboard': 'Bảng điều khiển',
                'Products': 'Sản phẩm',
                'View Products': 'Xem sản phẩm',
                'Add Product': 'Thêm sản phẩm',
                'Suppliers': 'Nhà cung cấp',
                'View Suppliers': 'Xem nhà cung cấp',
                'Add Supplier': 'Thêm nhà cung cấp',
                'Purchase Orders': 'Đơn đặt hàng',
                'View Orders': 'Xem đơn hàng',
                'Create Order': 'Tạo đơn hàng',
                'Users': 'Người dùng',
                'View Users': 'Xem người dùng',
                'Add User': 'Thêm người dùng',
                'Reports': 'Báo cáo',
                'Settings': 'Cài đặt',
                'Total Products': 'Tổng sản phẩm',
                'Monthly Revenue': 'Doanh thu tháng',
                'Low Stock Items': 'Sản phẩm sắp hết',
                'Stock-Out Items': 'Sản phẩm hết hàng',
                'Language': 'Ngôn ngữ',
                'Save': 'Lưu',
                'Cancel': 'Hủy',
                'Delete': 'Xóa',
                'Edit': 'Sửa',
                'View': 'Xem',
                'Search': 'Tìm kiếm',
                'Login': 'Đăng nhập',
                'Logout': 'Đăng xuất'
            },
            zh: { // Chinese
                'Dashboard': '仪表板',
                'Products': '产品',
                'View Products': '查看产品',
                'Add Product': '添加产品',
                'Suppliers': '供应商',
                'View Suppliers': '查看供应商',
                'Add Supplier': '添加供应商',
                'Purchase Orders': '采购订单',
                'View Orders': '查看订单',
                'Create Order': '创建订单',
                'Users': '用户',
                'View Users': '查看用户',
                'Add User': '添加用户',
                'Reports': '报告',
                'Settings': '设置',
                'Total Products': '总产品数',
                'Monthly Revenue': '月收入',
                'Low Stock Items': '低库存商品',
                'Stock-Out Items': '缺货商品',
                'Language': '语言',
                'Save': '保存',
                'Cancel': '取消',
                'Delete': '删除',
                'Edit': '编辑',
                'View': '查看',
                'Search': '搜索',
                'Login': '登录',
                'Logout': '登出'
            }
        };

        const langTranslations = translations[targetLanguage];
        return langTranslations && langTranslations[text] ? langTranslations[text] : null;
    }

    // Initialize the language selector widget
    initializeWidget() {
        const widget = document.createElement('div');
        widget.className = 'language-translator-widget';
        widget.innerHTML = `
            <div class="language-selector">
                <select id="languageSelect" class="language-dropdown">
                    <option value="en" ${this.currentLanguage === 'en' ? 'selected' : ''}>🇺🇸 English</option>
                    <option value="so" ${this.currentLanguage === 'so' ? 'selected' : ''}>🇸🇴 Somali</option>
                    <option value="vi" ${this.currentLanguage === 'vi' ? 'selected' : ''}>🇻🇳 Vietnamese</option>
                    <option value="zh" ${this.currentLanguage === 'zh' ? 'selected' : ''}>🇨🇳 Chinese</option>
                </select>
            </div>
        `;

        // Add to body as floating widget
        document.body.appendChild(widget);

        // Add event listener
        const select = widget.querySelector('#languageSelect');
        select.addEventListener('change', async (e) => {
            await this.changeLanguage(e.target.value);
        });

        // Apply current language
        this.applyTranslations();
    }

    // Change language and apply translations
    async changeLanguage(language) {
        try {
            this.currentLanguage = language;
            localStorage.setItem('preferred_language', language);
            
            // Clear stored original texts to rebuild from current state
            if (this.originalTexts) {
                this.originalTexts.clear();
            }
            
            this.showNotification('Translating page...', 'info');
            await this.applyTranslations();
            
            // Show success message
            const successMsg = await this.translateText('Language changed successfully', language);
            this.showNotification(successMsg, 'success');
        } catch (e) {
            console.error('Error changing language:', e);
            this.showNotification('Error changing language', 'error');
        }
    }

    // Apply translations to current page
    async applyTranslations() {
        if (this.isTranslating) return;
        this.isTranslating = true;

        try {
            // Translate elements by text content
            await this.translateByContent();
            
            // Re-initialize any event handlers that might be affected
            this.reinitializeEventHandlers();
        } finally {
            this.isTranslating = false;
        }
    }

    // Re-initialize event handlers after translation
    reinitializeEventHandlers() {
        try {
            // Ensure CRUD operation buttons have proper classes and attributes
            const crudButtons = document.querySelectorAll('.updateProduct, .deleteProduct, .updateSupplier, .deleteSupplier, .updateUser, .deleteUser');
            crudButtons.forEach(button => {
                // Ensure the button maintains its functionality
                if (!button.hasAttribute('data-functionality-preserved')) {
                    button.setAttribute('data-functionality-preserved', 'true');
                }
            });
        } catch (e) {
            console.warn('Error reinitializing event handlers:', e);
        }
    }

    // Translate elements by matching text content using Google Translate API
    async translateByContent() {
        try {
            // Store original translations to prevent back-and-forth switching
            if (!this.originalTexts) {
                this.originalTexts = new Map();
            }

            const elementsToTranslate = [
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'span:not(.stat_number):not(.stock-badge)', 'span[data-translatable="true"]', 'button:not(.updateProduct):not(.deleteProduct):not(.updateSupplier):not(.deleteSupplier):not(.updateUser):not(.deleteUser)', 'label',
                '.nav-header', '.sidebar_nav span', '.sidebar_nav a:not(.updateProduct):not(.deleteProduct):not(.updateSupplier):not(.deleteSupplier):not(.updateUser):not(.deleteUser)',
                '.card_header h3', '.alert', 'p:not(.productCount):not(.userCount):not(.supplierCount)', 'th',
                'td:not([data-no-translate="true"]):not(:has(.updateProduct)):not(:has(.deleteProduct)):not(:has(.updateSupplier)):not(:has(.deleteSupplier)):not(:has(.updateUser)):not(:has(.deleteUser))',
                'input[type="text"]', 'input[type="email"]', 'textarea',
                '.modal-title'
            ];

            const translationPromises = [];

            elementsToTranslate.forEach(selector => {
                try {
                    const elements = document.querySelectorAll(selector);
                    elements.forEach(element => {
                        try {
                            // Skip elements that contain only numbers or special content
                            if (element.classList.contains('stat_number') || 
                                element.hasAttribute('data-no-translate') ||
                                element.classList.contains('updateProduct') ||
                                element.classList.contains('deleteProduct') ||
                                element.classList.contains('updateSupplier') ||
                                element.classList.contains('deleteSupplier') ||
                                element.classList.contains('updateUser') ||
                                element.classList.contains('deleteUser') ||
                                element.closest('td')?.querySelector('.updateProduct') ||
                                element.closest('td')?.querySelector('.deleteProduct') ||
                                /^\$?[\d,\.]+%?$/.test(element.textContent.trim()) ||
                                /^\d+$/.test(element.textContent.trim()) ||
                                element.textContent.trim() === '') {
                                return;
                            }

                            let currentText = element.textContent.trim();
                            
                            // Skip if already processed with same language
                            if (element.dataset.translatedTo === this.currentLanguage) {
                                return;
                            }
                            
                            // Store original English text if not already stored
                            if (!this.originalTexts.has(element)) {
                                if (!element.dataset.originalText) {
                                    element.dataset.originalText = currentText;
                                }
                                this.originalTexts.set(element, element.dataset.originalText);
                            }

                            // Get the original text to translate from
                            let textToTranslate = element.dataset.originalText || this.originalTexts.get(element) || currentText;
                            
                            if (textToTranslate && textToTranslate.length > 0 && textToTranslate.length < 500) {
                                // Add to translation promises
                                const translationPromise = this.translateText(textToTranslate, this.currentLanguage)
                                    .then(translation => {
                                        if (translation && translation !== currentText) {
                                            element.textContent = translation;
                                            element.dataset.translatedTo = this.currentLanguage;
                                        }
                                    })
                                    .catch(e => console.warn('Error translating element:', e));
                                
                                translationPromises.push(translationPromise);
                            }
                        } catch (e) {
                            console.warn('Error translating element:', e);
                        }
                    });
                } catch (e) {
                    console.warn('Error selecting elements with selector:', selector, e);
                }
            });

            // Wait for all translations to complete
            await Promise.all(translationPromises);

            // Also translate placeholder text and input values
            await this.translateAttributes();

        } catch (e) {
            console.error('Error in translateByContent:', e);
        }
    }

    // Translate attributes like placeholders
    async translateAttributes() {
        const elementsWithPlaceholders = document.querySelectorAll('input[placeholder], textarea[placeholder]');
        const attributePromises = [];

        elementsWithPlaceholders.forEach(element => {
            if (!element.dataset.originalPlaceholder) {
                element.dataset.originalPlaceholder = element.placeholder;
            }
            
            const originalPlaceholder = element.dataset.originalPlaceholder;
            if (originalPlaceholder && originalPlaceholder.trim() !== '') {
                const promise = this.translateText(originalPlaceholder, this.currentLanguage)
                    .then(translation => {
                        if (translation) {
                            element.placeholder = translation;
                        }
                    });
                attributePromises.push(promise);
            }
        });

        await Promise.all(attributePromises);
    }



    // Show notification
    showNotification(message, type = 'info') {
        try {
            const notification = document.createElement('div');
            notification.className = `translator-notification ${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        } catch (e) {
            console.error('Error showing notification:', e);
        }
    }

    // Get current language
    getCurrentLanguage() {
        return this.currentLanguage;
    }
}

// Initialize translator when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    try {
        window.translator = new LanguageTranslator();
        console.log('Language translator initialized successfully');
    } catch (e) {
        console.error('Error initializing language translator:', e);
    }
});

// Auto-refresh translations when page content changes (throttled)
let translationTimeout;
const observer = new MutationObserver(function(mutations) {
    if (window.translator && !window.translator.isTranslating && !translationTimeout) {
        translationTimeout = setTimeout(async () => {
            try {
                await window.translator.applyTranslations();
            } catch (e) {
                console.warn('Error applying translations:', e);
            }
            translationTimeout = null;
        }, 1000); // Throttle to 1 second to avoid API rate limits
    }
});

// Start observing after DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    try {
        if (document.body) {
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    } catch (e) {
        console.warn('Error starting mutation observer:', e);
    }
});