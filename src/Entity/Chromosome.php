<?php
/**
 *    Copyright 2015-2018 Mathieu Piot.
 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Chromosome entity.
 *
 * @ORM\Table(name="chromosome")
 * @ORM\Entity(repositoryClass="App\Repository\ChromosomeRepository")
 */
class Chromosome
{
    /**
     * The ID in the database.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The name of the chromosome.
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * An array of accessions.
     *
     * @var array
     *
     * @ORM\Column(name="accessions", type="array", nullable=true)
     */
    private $accessions;

    /**
     * The chromosome description.
     *
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * An array of keywords for the chromosome.
     *
     * @var array
     *
     * @ORM\Column(name="keywords", type="array")
     */
    private $keywords;

    /**
     * The project ID.
     *
     * @var string
     *
     * @ORM\Column(name="projectId", type="string", length=255, nullable=true)
     */
    private $projectId;

    /**
     * When the chromosome was created.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;

    /**
     * The num created.
     *
     * @var int
     *
     * @ORM\Column(name="numCreated", type="integer", nullable=true)
     */
    private $numCreated;

    /**
     * When the chromosome was released.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="dateReleased", type="datetime")
     */
    private $dateReleased;

    /**
     * The numReleased.
     *
     * @var int
     *
     * @ORM\Column(name="numReleased", type="integer", nullable=true)
     */
    private $numReleased;

    /**
     * The version of the chromosome.
     *
     * @var int
     *
     * @ORM\Column(name="numVersion", type="integer", nullable=true)
     */
    private $numVersion;

    /**
     * The length of the chromosome.
     *
     * @var int
     *
     * @ORM\Column(name="length", type="integer")
     */
    private $length;

    /**
     * The G/C percent.
     *
     * @var float
     *
     * @ORM\Column(name="gc", type="float")
     */
    private $gc;

    /**
     * The number of CDS.
     *
     * @var int
     *
     * @ORM\Column(name="cdsCount", type="integer")
     */
    private $cdsCount;

    /**
     * Is it mitochondrial ?
     * true -> yes, false -> no.
     *
     * @var bool
     *
     * @ORM\Column(name="mitochondrial", type="boolean")
     */
    private $mitochondrial;

    /**
     * A comment on this chromosome.
     *
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * The strain that owned the chromosome.
     *
     * @var Strain
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Strain", inversedBy="chromosomes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $strain;

    /**
     * The DNA sequence of the chromosome.
     *
     * @var DnaSequence
     *
     * @ORM\OneToOne(targetEntity="App\Entity\DnaSequence", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $dnaSequence;

    /**
     * Flat files of the chromsomes.
     *
     * @var FlatFile|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\FlatFile", mappedBy="chromosome", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $flatFiles;

    /**
     * A slug for url.
     *
     * @var string
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=128, unique=true)
     */
    private $slug;

    /**
     * The source.
     *
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=255)
     */
    private $source;

    /**
     * Locus.
     *
     * @var Locus|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Locus", mappedBy="chromosome", cascade={"persist", "remove"})
     */
    private $locus;

    /**
     * Chromosome constructor.
     */
    public function __construct()
    {
        $this->accessions = [];
        $this->keywords = [];
        $this->flatFiles = new ArrayCollection();
        $this->seos = new ArrayCollection();
        $this->locus = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Chromosome
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add accession.
     *
     * @param string $accession
     *
     * @return Chromosome
     */
    public function addAccession($accession)
    {
        if (!empty($accession) && !in_array($accession, $this->accessions, true)) {
            $this->accessions[] = $accession;
        }

        return $this;
    }

    /**
     * Remove accession.
     *
     * @param string $accession
     *
     * @return Chromosome
     */
    public function removeAccession($accession)
    {
        if (false !== $key = array_search($accession, $this->accessions, true)) {
            unset($this->accessions[$key]);
            $this->accessions = array_values($this->accessions);
        }

        return $this;
    }

    /**
     * Empty accessions.
     *
     * @return Chromosome
     */
    public function emptyAccessions()
    {
        $this->accessions = [];

        return $this;
    }

    /**
     * Set accession.
     *
     * @param array $accessions
     *
     * @return Chromosome
     */
    public function setAccession($accessions)
    {
        if (null !== $accessions) {
            foreach ($accessions as $accession) {
                $this->addAccession($accession);
            }
        }

        return $this;
    }

    /**
     * Get accession.
     *
     * @return array
     */
    public function getAccessions()
    {
        return $this->accessions;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Chromosome
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add keyword.
     *
     * @param string $keyword
     *
     * @return Chromosome
     */
    public function addKeyword($keyword)
    {
        if (!empty($keyword) && !in_array($keyword, $this->keywords, true)) {
            $this->keywords[] = $keyword;
        }

        return $this;
    }

    /**
     * Remove keyword.
     *
     * @param string $keyword
     *
     * @return Chromosome
     */
    public function removeKeyword($keyword)
    {
        if (false !== $key = array_search($keyword, $this->keywords, true)) {
            unset($this->keywords[$key]);
            $this->keywords = array_values($this->keywords);
        }

        return $this;
    }

    /**
     * Empty keywords.
     *
     * @return Chromosome
     */
    public function emptyKeywords()
    {
        $this->keywords = [];

        return $this;
    }

    /**
     * Set keywords.
     *
     * @param array $keywords
     *
     * @return Chromosome
     */
    public function setKeywords($keywords)
    {
        if (null === $keywords) {
            return $this;
        }

        foreach ($keywords as $keyword) {
            $this->addKeyword($keyword);
        }

        return $this;
    }

    /**
     * Get keywords.
     *
     * @return array
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set projectId.
     *
     * @param string $projectId
     *
     * @return Chromosome
     */
    public function setProjectId($projectId = null)
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Get projectId.
     *
     * @return string
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * Set dateCreated.
     *
     * @param \DateTime $dateCreated
     *
     * @return Chromosome
     */
    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated.
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set numCreated.
     *
     * @param int $numCreated
     *
     * @return Chromosome
     */
    public function setNumCreated($numCreated)
    {
        $this->numCreated = $numCreated;

        return $this;
    }

    /**
     * Get numCreated.
     *
     * @return int
     */
    public function getNumCreated()
    {
        return $this->numCreated;
    }

    /**
     * Set dateReleased.
     *
     * @param \DateTime $dateReleased
     *
     * @return Chromosome
     */
    public function setDateReleased(\DateTime $dateReleased)
    {
        $this->dateReleased = $dateReleased;

        return $this;
    }

    /**
     * Get dateReleased.
     *
     * @return \DateTime
     */
    public function getDateReleased()
    {
        return $this->dateReleased;
    }

    /**
     * Set numReleased.
     *
     * @param int $numReleased
     *
     * @return Chromosome
     */
    public function setNumReleased($numReleased)
    {
        $this->numReleased = $numReleased;

        return $this;
    }

    /**
     * Get numReleased.
     *
     * @return int
     */
    public function getNumReleased()
    {
        return $this->numReleased;
    }

    /**
     * Set numVersion.
     *
     * @param int $numVersion
     *
     * @return Chromosome
     */
    public function setNumVersion($numVersion)
    {
        $this->numVersion = $numVersion;

        return $this;
    }

    /**
     * Get numVersion.
     *
     * @return int
     */
    public function getNumVersion()
    {
        return $this->numVersion;
    }

    /**
     * Set length.
     *
     * @param int $length
     *
     * @return Chromosome
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get length.
     *
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set gc.
     *
     * @param float $gc
     *
     * @return Chromosome
     */
    public function setGc($gc)
    {
        $this->gc = $gc;

        return $this;
    }

    /**
     * Get gc.
     *
     * @return float
     */
    public function getGc()
    {
        return $this->gc;
    }

    /**
     * Set cdsCount.
     *
     * @param int $cdsCount
     *
     * @return Chromosome
     */
    public function setCdsCount($cdsCount)
    {
        $this->cdsCount = $cdsCount;

        return $this;
    }

    /**
     * Get cdsCount.
     *
     * @return int
     */
    public function getCdsCount()
    {
        return $this->cdsCount;
    }

    /**
     * Set mitochondrial.
     *
     * @param bool $mitochondrial
     *
     * @return Chromosome
     */
    public function setMitochondrial($mitochondrial)
    {
        $this->mitochondrial = $mitochondrial;

        return $this;
    }

    /**
     * Get mitochondrial.
     *
     * @return bool
     */
    public function getMitochondrial()
    {
        return $this->mitochondrial;
    }

    /**
     * Set comment.
     *
     * @param string $comment
     *
     * @return Chromosome
     */
    public function setComment($comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set strain.
     *
     * @param Strain $strain
     *
     * @return $this
     */
    public function setStrain(Strain $strain)
    {
        $this->strain = $strain;

        return $this;
    }

    /**
     * Get strain.
     *
     * @return Strain
     */
    public function getStrain()
    {
        return $this->strain;
    }

    /**
     * Set DnaSequence.
     *
     * @param DnaSequence $dnaSequence
     *
     * @return $this
     */
    public function setDnaSequence(DnaSequence $dnaSequence)
    {
        $this->dnaSequence = $dnaSequence;

        return $this;
    }

    /**
     * Get DnaSequence.
     *
     * @return DnaSequence
     */
    public function getDnaSequence()
    {
        return $this->dnaSequence;
    }

    /**
     * Add FlatFile.
     *
     * @param FlatFile $flatFile
     *
     * @return $this
     */
    public function addFlatFile(FlatFile $flatFile)
    {
        if (!$this->flatFiles->contains($flatFile)) {
            $this->flatFiles[] = $flatFile;
            $flatFile->setChromosome($this);
        }

        return $this;
    }

    /**
     * Remove FlatFile.
     *
     * @param FlatFile $flatFile
     *
     * @return $this
     */
    public function removeFlatFile(FlatFile $flatFile)
    {
        if ($this->flatFiles->contains($flatFile)) {
            $this->flatFiles->removeElement($flatFile);
        }

        return $this;
    }

    /**
     * Get FlatFile.
     *
     * @return FlatFile|ArrayCollection
     */
    public function getFlatFiles()
    {
        return $this->flatFiles;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Chromosome
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set source.
     *
     * @param string $slug
     *
     * @return Chromosome
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Add Locus.
     *
     * @param Locus $locus
     *
     * @return $this
     */
    public function addLocus(Locus $locus)
    {
        if (!$this->locus->contains($locus)) {
            $this->locus[] = $locus;
            $locus->setChromosome($this);
        }

        return $this;
    }

    /**
     * Remove Locus.
     *
     * @param FlatFile $flatFile
     *
     * @return $this
     */
    public function removeLocus(Locus $locus)
    {
        if ($this->locus->contains($locus)) {
            $this->locus->removeElement($locus);
        }

        return $this;
    }

    /**
     * Get Locus.
     *
     * @return Locus|ArrayCollection
     */
    public function getLocus()
    {
        return $this->locus;
    }
}
