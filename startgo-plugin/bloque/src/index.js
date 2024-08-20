import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ColorPalette } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { select } from '@wordpress/data';
import './style.css';

registerBlockType('startgo-plugin/formulario-bloque', {
    title: __('Bloque de Formulario', 'startgo-plugin'),
    icon: 'feedback',
    category: 'widgets',
    attributes: {
        colorFondo: {
            type: 'string',
            default: '#ffffff',
        },
        userNombre: {
            type: 'string',
            default: '',
        },
        userApellido: {
            type: 'string',
            default: '',
        },
        userEmail: {
            type: 'string',
            default: '',
        },
    },
    edit({ attributes, setAttributes }) {
        const { colorFondo, userNombre, userApellido, userEmail } = attributes;
        const blockProps = useBlockProps({ style: { backgroundColor: colorFondo } });

        useEffect(() => {
            const currentUser = select('core').getCurrentUser();
            if (currentUser) {
                setAttributes({
                    userNombre: currentUser.first_name || '',
                    userApellido: currentUser.last_name || '',
                    userEmail: currentUser.email || '',
                });
            }
        }, []);

        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Color de Fondo', 'startgo-plugin')}>
                        <ColorPalette
                            value={colorFondo}
                            onChange={(nuevoColor) => setAttributes({ colorFondo: nuevoColor })}
                        />
                    </PanelBody>
                </InspectorControls>
                <div {...blockProps}>
                    <div id="alert-container"></div>
                    <form className="formulario-sugerencias">
                        <input 
                            className="form-control nombre" 
                            type="text" 
                            name="nombre" 
                            placeholder={__('Nombre', 'startgo-plugin')} 
                            value={userNombre}
                            onChange={(e) => setAttributes({ userNombre: e.target.value })}
                        />
                        <input 
                            className="form-control apellido" 
                            type="text" 
                            name="apellido" 
                            placeholder={__('Apellido', 'startgo-plugin')} 
                            value={userApellido}
                            onChange={(e) => setAttributes({ userApellido: e.target.value })}
                        />
                        <input 
                            className="form-control email" 
                            type="email" 
                            name="email" 
                            placeholder={__('Correo Electrónico', 'startgo-plugin')} 
                            value={userEmail}
                            onChange={(e) => setAttributes({ userEmail: e.target.value })}
                        />
                        <textarea className="form-control sugerencias" name="sugerencias" placeholder={__('Sugerencias', 'startgo-plugin')}></textarea>
                        <select id="pais" name="pais" className="form-control pais"></select>
                        <button type="submit" className="btn btn-primary">{__('Enviar', 'startgo-plugin')}</button>
                    </form>
                </div>
            </>
        );
    },
    save({ attributes }) {
        const { colorFondo, userNombre, userApellido, userEmail } = attributes;
        const blockProps = useBlockProps.save({ style: { backgroundColor: colorFondo } });

        return (
            <div {...blockProps}>
                <div id="alert-container"></div>
                <form className="formulario-sugerencias">
                    <input className="form-control nombre" type="text" name="nombre" placeholder={__('Nombre', 'startgo-plugin')} defaultValue={userNombre} />
                    <input className="form-control apellido" type="text" name="apellido" placeholder={__('Apellido', 'startgo-plugin')} defaultValue={userApellido} />
                    <input className="form-control email" type="email" name="email" placeholder={__('Correo Electrónico', 'startgo-plugin')} defaultValue={userEmail} />
                    <textarea className="form-control sugerencias" name="sugerencias" placeholder={__('Sugerencias', 'startgo-plugin')}></textarea>
                    <select id="pais" name="pais" className="form-control pais"></select>
                    <button type="submit" className="btn btn-primary">{__('Enviar', 'startgo-plugin')}</button>
                </form>
            </div>
        );
    },
});
