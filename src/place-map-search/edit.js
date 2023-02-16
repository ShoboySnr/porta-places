/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {
    PanelBody,
    PanelRow,
    SelectControl,
} from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
    const { serviceArea } = attributes;
    const blockProps = useBlockProps();

    //Get category list
    const cats = wp.data.select('core').getEntityRecords('taxonomy', 'gd_placecategory');

    // returns an entire array of taxonomy term data
    const options = cats? cats.map((cat) => {
        return {
            label: cat.name,
            value: cat.slug,
        };
    }):   [];

    options.unshift({label: 'Select an option', value: ''});

    return (
        <>
            <InspectorControls>
                <PanelBody
                    title={__('Category Settings', 'cvgt-locations')}
                    initialOpen={true}
                >
                    <PanelRow>
                        <fieldset>
                            <SelectControl
                                label={__('Select a Service Area', 'cvgt-locations')}
                                options={options}
                                value={serviceArea}
                                onChange={(value) => {
                                    setAttributes({ serviceArea: value });
                                }}
                            />
                        </fieldset>
                    </PanelRow>
                </PanelBody>
            </InspectorControls>
            <div {...blockProps}>
                <ServerSideRender
                    block="mrkwp/porta-place-map-search"
                    attributes={attributes}
                />
            </div>
        </>
    );
}
