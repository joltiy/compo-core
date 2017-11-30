<?php

namespace Compo\Sonata\AdminBundle\Form;

class FormMapper extends \Sonata\AdminBundle\Form\FormMapper
{
    /**
     * Add new tab.
     *
     * @param string $name
     * @param array  $options
     *
     * @return $this
     */
    public function tab($name, array $options = array())
    {
        if (!isset($options['label'])) {
            $options['label'] = 'form.tab_' . $name;
        }

        return parent::tab($name, $options);
    }

    /**
     * Add new group or tab (if parameter "tab=true" is available in options).
     *
     * @param string $name
     * @param array  $options
     *
     * @return $this
     *
     * @throws \RuntimeException
     */
    public function with($name, array $options = array())
    {
        /*
         * The current implementation should work with the following workflow:
         *
         *     $formMapper
         *        ->with('group1')
         *            ->add('username')
         *            ->add('password')
         *        ->end()
         *        ->with('tab1', array('tab' => true))
         *            ->with('group1')
         *                ->add('username')
         *                ->add('password')
         *            ->end()
         *            ->with('group2', array('collapsed' => true))
         *                ->add('enabled')
         *                ->add('createdAt')
         *            ->end()
         *        ->end();
         *
         */
        $defaultOptions = array(
            'collapsed' => false,
            'class' => false,
            'description' => false,
//            'label' => $name, // NEXT_MAJOR: Remove this line and uncomment the next one
            'label' => $this->admin->getLabelTranslatorStrategy()->getLabel($name, $this->getName(), 'group'),
            'translation_domain' => null,
            'name' => $name,
            'box_class' => 'box box-primary',
        );

        $code = $name;

        // Open
        if (array_key_exists('tab', $options) && $options['tab']) {
            $tabs = $this->getTabs();

            if ($this->currentTab) {
                if (isset($tabs[$this->currentTab]['auto_created']) && true === $tabs[$this->currentTab]['auto_created']) {
                    throw new \RuntimeException('New tab was added automatically when you have added field or group. You should close current tab before adding new one OR add tabs before adding groups and fields.');
                }

                throw new \RuntimeException(sprintf('You should close previous tab "%s" with end() before adding new tab "%s".', $this->currentTab, $name));
            } elseif ($this->currentGroup) {
                throw new \RuntimeException(sprintf('You should open tab before adding new group "%s".', $name));
            }

            if (!isset($tabs[$name])) {
                $tabs[$name] = array();
            }

            $tabs[$code] = array_merge($defaultOptions, array(
                'auto_created' => false,
                'groups' => array(),
            ), $tabs[$code], $options);

            $this->currentTab = $code;
        } else {
            if ($this->currentGroup) {
                throw new \RuntimeException(sprintf('You should close previous group "%s" with end() before adding new tab "%s".', $this->currentGroup, $name));
            }

            if (!$this->currentTab) {
                // no tab define
                $this->with('default', array(
                    'tab' => true,
                    'auto_created' => true,
                    'translation_domain' => isset($options['translation_domain']) ? $options['translation_domain'] : null,
                )); // add new tab automatically
            }

            // if no tab is selected, we go the the main one named '_' ..
            if ('default' !== $this->currentTab) {
                $code = $this->currentTab . '.' . $name; // groups with the same name can be on different tabs, so we prefix them in order to make unique group name
            }

            $groups = $this->getGroups();
            if (!isset($groups[$code])) {
                $groups[$code] = array();
            }

            $groups[$code] = array_merge($defaultOptions, array(
                'fields' => array(),
            ), $groups[$code], $options);

            $this->currentGroup = $code;
            $this->setGroups($groups);
            $tabs = $this->getTabs();
        }

        if ($this->currentGroup && isset($tabs[$this->currentTab]) && !in_array($this->currentGroup, $tabs[$this->currentTab]['groups'])) {
            $tabs[$this->currentTab]['groups'][] = $this->currentGroup;
        }

        $this->setTabs($tabs);

        return $this;
    }
}
