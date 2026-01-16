<script setup>
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
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
    ListBulletIcon,
} from '@heroicons/vue/24/outline';
import { CheckIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    module: Object,
    settings: Object,
    defaultSettings: Object,
});

const form = useForm({
    settings: JSON.parse(JSON.stringify(props.settings)),
});

const submit = () => {
    form.put(route('admin.shipping.flat.settings.update'));
};

const resetAll = () => {
    if (confirm('Reset all settings to defaults?')) {
        form.settings = JSON.parse(JSON.stringify(props.defaultSettings));
    }
};

const hasChanges = computed(() => {
    return JSON.stringify(form.settings) !== JSON.stringify(props.settings);
});
</script>

<template>
    <AdminLayout :title="`${module.name} Settings`">
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
                            <p class="text-sm text-gray-500">Shipping Method Configuration</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span v-if="hasChanges" class="text-sm text-amber-600 font-medium">
                        Unsaved changes
                    </span>
                    <button
                        type="button"
                        @click="resetAll"
                        class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors"
                    >
                        <ArrowPathIcon class="w-4 h-4 inline mr-2" />
                        Reset
                    </button>
                    <button
                        type="submit"
                        form="settings-form"
                        :disabled="form.processing || !hasChanges"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl hover:from-indigo-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-indigo-500/25"
                    >
                        <CheckIcon class="w-4 h-4 inline mr-2" />
                        Save Changes
                    </button>
                </div>
            </div>
        </template>

        <form id="settings-form" @submit.prevent="submit">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Left Sidebar - Module Info -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-5 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-900">Module Status</h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Status</span>
                                <span
                                    :class="form.settings.enabled ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                    class="px-3 py-1 text-xs font-semibold rounded-full"
                                >
                                    {{ form.settings.enabled ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Version</span>
                                <span class="text-sm font-mono text-gray-900">v{{ module.installed_version }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Type</span>
                                <span class="text-sm text-gray-900">Shipping</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-5 text-white">
                        <div class="flex items-center space-x-3 mb-4">
                            <CurrencyDollarIcon class="w-8 h-8 opacity-80" />
                            <div>
                                <p class="text-sm opacity-80">Current Rate</p>
                                <p class="text-2xl font-bold">${{ form.settings.cost?.toFixed(2) || '0.00' }}</p>
                            </div>
                        </div>
                        <div v-if="form.settings.free_shipping_threshold" class="pt-4 border-t border-white/20">
                            <div class="flex items-center space-x-2">
                                <GiftIcon class="w-5 h-5 opacity-80" />
                                <span class="text-sm opacity-80">Free shipping over ${{ form.settings.free_shipping_threshold }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">
                        <div class="flex items-start space-x-3">
                            <InformationCircleIcon class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" />
                            <div>
                                <h4 class="text-sm font-medium text-blue-900">Need Help?</h4>
                                <p class="text-sm text-blue-700 mt-1">
                                    Flat rate shipping charges a fixed cost regardless of cart weight or quantity.
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
                                    <h3 class="font-semibold text-gray-900">Enable Shipping Method</h3>
                                    <p class="text-sm text-gray-500">Make this shipping option available at checkout</p>
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
                                <h2 class="font-semibold text-gray-900">General Settings</h2>
                            </div>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Title -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Display Title <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        v-model="form.settings.title"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                        placeholder="e.g., Standard Shipping"
                                    />
                                </div>

                                <!-- Sort Order -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                                    <input
                                        type="number"
                                        v-model.number="form.settings.sort_order"
                                        min="0"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                        placeholder="0"
                                    />
                                    <p class="mt-1.5 text-xs text-gray-500">Lower numbers appear first</p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea
                                    v-model="form.settings.description"
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                                    placeholder="Brief description shown to customers at checkout..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Settings -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center space-x-3">
                                <CurrencyDollarIcon class="w-5 h-5 text-gray-400" />
                                <h2 class="font-semibold text-gray-900">Pricing</h2>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Shipping Cost -->
                                <div class="bg-gray-50 rounded-xl p-5">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Shipping Cost
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">$</span>
                                        <input
                                            type="number"
                                            v-model.number="form.settings.cost"
                                            step="0.01"
                                            min="0"
                                            class="w-full pl-10 pr-4 py-3 text-lg font-semibold border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                            placeholder="0.00"
                                        />
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">Fixed rate charged for every order</p>
                                </div>

                                <!-- Free Shipping Threshold -->
                                <div class="bg-green-50 rounded-xl p-5 border border-green-100">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        <GiftIcon class="w-4 h-4 inline mr-1 text-green-600" />
                                        Free Shipping Threshold
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">$</span>
                                        <input
                                            type="number"
                                            v-model.number="form.settings.free_shipping_threshold"
                                            step="0.01"
                                            min="0"
                                            class="w-full pl-10 pr-4 py-3 text-lg font-semibold border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-white"
                                            placeholder="No threshold"
                                        />
                                    </div>
                                    <p class="mt-2 text-xs text-gray-600">Orders above this amount get free shipping</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>