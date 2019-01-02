import { Component } from 'src/core/shopware';
import template from './sw-condition-not-found.html.twig';

/**
 * @public
 * @description TODO: Add description
 * @status prototype
 * @example-type code-only
 * @component-example
 * <sw-condition-placeholder :condition="condition"></sw-condition-and-container>
 */
Component.extend('sw-condition-not-found', 'sw-condition-placeholder', {
    template,
    computed: {
        errorMessage() {
            const fields = JSON.stringify(this.condition.value);
            return this.$tc('global.sw-condition-group.condition.not-found.error-message',
                Object.keys(this.condition.value).length,
                { type: this.condition.type, fields });
        }
    },
    methods: {
        mountComponent() {
            // nothing to do
        }
    }
});
