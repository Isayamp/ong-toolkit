/**
 * AMIDAC — Main JavaScript (MODIFIÉ)
 * Développé par Isaya M'lela Pombo — Emo Touch Group
 * 
 * MODIFICATIONS: Suppression des fonctions header-related
 * (stickyHeader, backToTop, topBar) pour utiliser header custom
 *
 * @package AMIDAC_Kadence_Child
 * @version 1.0.0 (MODIFIED)
 */

( function( $ ) {
    'use strict';

    // ========================================================================
    // INIT
    // ========================================================================

    $( document ).ready( function() {
        AMIDAC.init();
    } );

    const AMIDAC = {

        init() {
            this.mobileMenu();
            this.smoothScroll();
            this.animateOnScroll();
            this.counterAnimation();
        },

        // ====================================================================
        // MENU MOBILE
        // ====================================================================

        mobileMenu() {
            const $toggle  = $( '.amidac-menu-toggle, .menu-toggle' );
            const $nav     = $( '.main-navigation, .amidac-nav' );
            const $overlay = $( '.amidac-nav-overlay' );

            $toggle.on( 'click', function() {
                const isOpen = $nav.hasClass( 'is-open' );
                $nav.toggleClass( 'is-open' );
                $( this ).attr( 'aria-expanded', ! isOpen );
                $overlay.toggleClass( 'is-visible' );
                $( 'body' ).toggleClass( 'nav-open' );
            } );

            $overlay.on( 'click', function() {
                $nav.removeClass( 'is-open' );
                $toggle.attr( 'aria-expanded', false );
                $overlay.removeClass( 'is-visible' );
                $( 'body' ).removeClass( 'nav-open' );
            } );

            // Fermer avec Escape
            $( document ).on( 'keydown', function( e ) {
                if ( e.key === 'Escape' ) {
                    $nav.removeClass( 'is-open' );
                    $toggle.attr( 'aria-expanded', false );
                    $overlay.removeClass( 'is-visible' );
                    $( 'body' ).removeClass( 'nav-open' );
                }
            } );
        },

        // ====================================================================
        // DÉFILEMENT DOUX
        // ====================================================================

        smoothScroll() {
            $( 'a[href*="#"]' ).not( '[href="#"]' ).not( '[href="#0"]' ).on( 'click', function( e ) {
                const target = $( this.hash );
                if ( target.length ) {
                    e.preventDefault();
                    const offset = $( '.site-header' ).outerHeight() + 20;
                    $( 'html, body' ).animate( {
                        scrollTop: target.offset().top - offset
                    }, 600, 'swing' );
                }
            } );
        },

        // ====================================================================
        // ANIMATIONS AU SCROLL (INTERSECTION OBSERVER)
        // ====================================================================

        animateOnScroll() {
            if ( ! ( 'IntersectionObserver' in window ) ) return;

            const observer = new IntersectionObserver( ( entries ) => {
                entries.forEach( entry => {
                    if ( entry.isIntersecting ) {
                        entry.target.classList.add( 'is-animated' );
                        observer.unobserve( entry.target );
                    }
                } );
            }, {
                threshold: 0.15,
                rootMargin: '0px 0px -50px 0px',
            } );

            document.querySelectorAll( '.amidac-animate, .amidac-card, .wp-block-group' ).forEach( el => {
                observer.observe( el );
            } );
        },

        // ====================================================================
        // ANIMATION DES COMPTEURS
        // ====================================================================

        counterAnimation() {
            if ( ! ( 'IntersectionObserver' in window ) ) return;

            const observer = new IntersectionObserver( ( entries ) => {
                entries.forEach( entry => {
                    if ( entry.isIntersecting ) {
                        const el    = entry.target;
                        const end   = parseInt( el.dataset.count || el.textContent, 10 );
                        const dur   = parseInt( el.dataset.duration || 2000, 10 );
                        const step  = dur / end;
                        let current = 0;

                        const timer = setInterval( () => {
                            current += Math.ceil( end / ( dur / 16 ) );
                            if ( current >= end ) {
                                current = end;
                                clearInterval( timer );
                            }
                            el.textContent = current.toLocaleString( 'fr-FR' );
                        }, 16 );

                        observer.unobserve( el );
                    }
                } );
            }, { threshold: 0.5 } );

            document.querySelectorAll( '.amidac-counter, [data-count]' ).forEach( el => {
                observer.observe( el );
            } );
        },

    };

    // Exposer globalement si nécessaire
    window.AMIDAC = AMIDAC;

} )( jQuery );