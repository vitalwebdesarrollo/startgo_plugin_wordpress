import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ColorPalette } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { select } from '@wordpress/data';

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
                    <form>
                        <input 
                            type="text" 
                            name="nombre" 
                            placeholder="Nombre" 
                            value={userNombre}
                            onChange={(e) => setAttributes({ userNombre: e.target.value })}
                        />
                        <input 
                            type="text" 
                            name="apellido" 
                            placeholder="Apellido" 
                            value={userApellido}
                            onChange={(e) => setAttributes({ userApellido: e.target.value })}
                        />
                        <input 
                            type="email" 
                            name="email" 
                            placeholder="Correo Electrónico" 
                            value={userEmail}
                            onChange={(e) => setAttributes({ userEmail: e.target.value })}
                        />
                        <textarea name="sugerencias" placeholder="Sugerencias"></textarea>
                        <select id="pais" name="pais"></select>
                        <button type="submit">Enviar</button>
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
                <form>
                    <input type="text" name="nombre" placeholder="Nombre" defaultValue={userNombre} />
                    <input type="text" name="apellido" placeholder="Apellido" defaultValue={userApellido} />
                    <input type="email" name="email" placeholder="Correo Electrónico" defaultValue={userEmail} />
                    <textarea name="sugerencias" placeholder="Sugerencias"></textarea>
                    <select id="pais" name="pais"></select>
                    <button type="submit">Enviar</button>
                </form>
            </div>
        );
    },
});