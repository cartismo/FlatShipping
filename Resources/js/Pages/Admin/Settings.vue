<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useConfirmDialog } from '@/Composables/useConfirmDialog.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    TruckIcon,
    ArrowLeftIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    XCircleIcon,
    CurrencyDollarIcon,
    Cog6ToothIcon,
    InformationCircleIcon,
    GiftIcon,
} from '@heroicons/vue/24/outline';
import { CheckIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    module: Object,
    settings: Object,
    defaultSettings: Object,
    options: Object,
    translations: Object,
});

const t = (key, replacements = {}) => {
    let text = props.translations?.[key] || key;

    Object.keys(replacements).forEach((replacementKey) => {
        text = text.replace(`:${replacementKey}`, replacements[replacementKey]);
    });

    return text;
};

const { confirm } = useConfirmDialog();

const form = useForm({
    settings: JSON.parse(JSON.stringify(props.settings)),
});

const currency = computed(() => props.options?.currency || {});
const currencyLeft = computed(() => currency.value.symbol_left || '');
const currencyRight = computed(() => currency.value.symbol_right || (!currency.value.symbol_left ? (currency.value.symbol || currency.value.code || '') : ''));
const inputHasLeftCurrency = computed(() => currencyLeft.value.length > 0);
const inputHasRightCurrency = computed(() => currencyRight.value.length > 0);

const formatMoney = (amount) => {
    const value = Number(amount ?? 0).toFixed(2);

    if (currencyLeft.value) {
        return `${currencyLeft.value}${value}`;
    }

    if (currencyRight.value) {
        return `${value} ${currencyRight.value}`;
    }

    return value;
};

const submit = () => {
    form.put(route('admin.shipping.flat.settings.update'));
};

const resetAll = async () => {
    const confirmed = await confirm({
        title: t('reset'),
        message: t('reset_confirm'),
        variant: 'warning',
    });
    if (confirmed) {
        form.settings = JSON.parse(JSON.stringify(props.defaultSettings));
    }
};

const hasChanges = computed(() => {
    return JSON.stringify(form.settings) !== JSON.stringify(props.settings);
});

const errorMessages = computed(() => Object.values(form.errors || {}));
</script>

<template>
    <AdminLayout :title="`${module.name} - ${t('title')}`">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link
                        :href="route('admin.modules.installed.index')"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                    >
                        <ArrowLeftIcon class="w-5 h-5" />
                    </Link>
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg">
                            <TruckIcon class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">{{ module.name }}</h1>
                            <p class="text-sm text-gray-500">{{ t('title') }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span v-if="hasChanges" class="text-sm text-amber-600 font-medium">
                        {{ t('unsaved_changes') }}
                    </span>
                    <button
                        type="button"
                        @click="resetAll"
                        class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors"
                    >
                        <ArrowPathIcon class="w-4 h-4 inline mr-2" />
                        {{ t('reset') }}
                    </button>
                    <button
                        type="submit"
                        form="settings-form"
                        :disabled="form.processing || !hasChanges"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl hover:from-indigo-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-indigo-500/25"
                    >
                        <CheckIcon class="w-4 h-4 inline mr-2" />
                        {{ t('save_changes') }}
                    </button>
                </div>
            </div>
        </template>

        <form id="settings-form" @submit.prevent="submit">
            <div v-if="errorMessages.length" class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                <p class="font-medium text-red-900">{{ t('validation_failed') }}</p>
                <ul class="mt-2 list-disc list-inside space-y-1">
                    <li v-for="message in errorMessages" :key="message">{{ message }}</li>
                </ul>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Left Sidebar - Module Info -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-5 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-900">{{ t('module_status') }}</h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">{{ t('status') }}</span>
                                <span
                                    :class="form.settings.enabled ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                    class="px-3 py-1 text-xs font-semibold rounded-full"
                                >
                                    {{ form.settings.enabled ? t('active') : t('inactive') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">{{ t('version') }}</span>
                                <span class="text-sm font-mono text-gray-900">v{{ module.installed_version }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">{{ t('type') }}</span>
                                <span class="text-sm text-gray-900">{{ t('type_shipping') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-5 text-white">
                        <div class="flex items-center space-x-3 mb-4">
                            <CurrencyDollarIcon class="w-8 h-8 opacity-80" />
                            <div>
                                <p class="text-sm opacity-80">{{ t('current_rate') }}</p>
                                <p class="text-2xl font-bold">{{ formatMoney(form.settings.cost) }}</p>
                            </div>
                        </div>
                        <div v-if="form.settings.free_shipping_threshold" class="pt-4 border-t border-white/20">
                            <div class="flex items-center space-x-2">
                                <GiftIcon class="w-5 h-5 opacity-80" />
                                <span class="text-sm opacity-80">
                                    {{ t('free_over', { amount: formatMoney(form.settings.free_shipping_threshold) }) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">
                        <div class="flex items-start space-x-3">
                            <InformationCircleIcon class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" />
                            <div>
                                <h4 class="text-sm font-medium text-blue-900">{{ t('help_title') }}</h4>
                                <p class="text-sm text-blue-700 mt-1">
                                    {{ t('help_text') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Settings Forms -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Enable/Disable Toggle -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div :class="form.settings.enabled ? 'bg-green-100' : 'bg-gray-100'" class="p-3 rounded-xl transition-colors">
                                    <component :is="form.settings.enabled ? CheckCircleIcon : XCircleIcon"
                                        :class="form.settings.enabled ? 'text-green-600' : 'text-gray-400'"
                                        class="w-6 h-6"
                                    />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ t('enable_method') }}</h3>
                                    <p class="text-sm text-gray-500">{{ t('enable_method_desc') }}</p>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="form.settings.enabled = !form.settings.enabled"
                                :class="form.settings.enabled ? 'bg-green-500' : 'bg-gray-300'"
                                class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                            >
                                <span
                                    :class="form.settings.enabled ? 'translate-x-5' : 'translate-x-0'"
                                    class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out"
                                />
                            </button>
                        </div>
                    </div>

                    <!-- General Settings -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center space-x-3">
                                <Cog6ToothIcon class="w-5 h-5 text-gray-400" />
                                <h2 class="font-semibold text-gray-900">{{ t('general_settings') }}</h2>
                            </div>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Title -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ t('display_title') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        v-model="form.settings.title"
                                        :class="[
                                            'w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors',
                                            form.errors['settings.title'] ? 'border-red-300' : 'border-gray-300',
                                        ]"
                                        :placeholder="t('display_title_placeholder')"
                                    />
                                    <p v-if="form.errors['settings.title']" class="mt-2 text-xs text-red-600">{{ form.errors['settings.title'] }}</p>
                                </div>

                                <!-- Sort Order -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('sort_order') }}</label>
                                    <input
                                        type="number"
                                        v-model.number="form.settings.sort_order"
                                        min="0"
                                        :class="[
                                            'w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors',
                                            form.errors['settings.sort_order'] ? 'border-red-300' : 'border-gray-300',
                                        ]"
                                        placeholder="0"
                                    />
                                    <p v-if="form.errors['settings.sort_order']" class="mt-2 text-xs text-red-600">{{ form.errors['settings.sort_order'] }}</p>
                                    <p class="mt-1.5 text-xs text-gray-500">{{ t('sort_order_help') }}</p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('description') }}</label>
                                <textarea
                                    v-model="form.settings.description"
                                    rows="3"
                                    :class="[
                                        'w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none',
                                        form.errors['settings.description'] ? 'border-red-300' : 'border-gray-300',
                                    ]"
                                    :placeholder="t('description_placeholder')"
                                ></textarea>
                                <p v-if="form.errors['settings.description']" class="mt-2 text-xs text-red-600">{{ form.errors['settings.description'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Settings -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center space-x-3">
                                <CurrencyDollarIcon class="w-5 h-5 text-gray-400" />
                                <h2 class="font-semibold text-gray-900">{{ t('pricing') }}</h2>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Shipping Cost -->
                                <div class="bg-gray-50 rounded-xl p-5">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        {{ t('shipping_cost') }}
                                    </label>
                                    <div class="relative">
                                        <span
                                            v-if="inputHasLeftCurrency"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium"
                                        >
                                            {{ currencyLeft }}
                                        </span>
                                        <input
                                            type="number"
                                            v-model.number="form.settings.cost"
                                            step="0.01"
                                            min="0"
                                            :class="[
                                                'w-full py-3 text-lg font-semibold border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors',
                                                inputHasLeftCurrency ? 'pl-10' : 'pl-4',
                                                inputHasRightCurrency ? 'pr-14' : 'pr-4',
                                                form.errors['settings.cost'] ? 'border-red-300' : 'border-gray-300',
                                            ]"
                                            placeholder="0.00"
                                        />
                                        <span
                                            v-if="inputHasRightCurrency"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium"
                                        >
                                            {{ currencyRight }}
                                        </span>
                                    </div>
                                    <p v-if="form.errors['settings.cost']" class="mt-2 text-xs text-red-600">{{ form.errors['settings.cost'] }}</p>
                                    <p class="mt-2 text-xs text-gray-500">{{ t('shipping_cost_help') }}</p>
                                </div>

                                <!-- Free Shipping Threshold -->
                                <div class="bg-green-50 rounded-xl p-5 border border-green-100">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        <GiftIcon class="w-4 h-4 inline mr-1 text-green-600" />
                                        {{ t('free_shipping_threshold') }}
                                    </label>
                                    <div class="relative">
                                        <span
                                            v-if="inputHasLeftCurrency"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium"
                                        >
                                            {{ currencyLeft }}
                                        </span>
                                        <input
                                            type="number"
                                            v-model.number="form.settings.free_shipping_threshold"
                                            step="0.01"
                                            min="0"
                                            :class="[
                                                'w-full py-3 text-lg font-semibold border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-white',
                                                inputHasLeftCurrency ? 'pl-10' : 'pl-4',
                                                inputHasRightCurrency ? 'pr-14' : 'pr-4',
                                                form.errors['settings.free_shipping_threshold'] ? 'border-red-300' : 'border-gray-300',
                                            ]"
                                            :placeholder="t('free_shipping_threshold_placeholder')"
                                        />
                                        <span
                                            v-if="inputHasRightCurrency"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium"
                                        >
                                            {{ currencyRight }}
                                        </span>
                                    </div>
                                    <p v-if="form.errors['settings.free_shipping_threshold']" class="mt-2 text-xs text-red-600">{{ form.errors['settings.free_shipping_threshold'] }}</p>
                                    <p class="mt-2 text-xs text-gray-600">{{ t('free_shipping_threshold_help') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
